<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingCompanion;
use App\Models\BookingItem;
use App\Models\CashierShift;
use App\Models\CashMovement;
use App\Models\Companion;
use App\Models\Package;
use App\Models\Product;
use App\Models\Room;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    // Dashboard Kasir
    public function dashboard()
    {
        $user = Auth::user();
        $activeShift = CashierShift::where('user_id', $user->id)->where('status', 'open')->latest()->first();
        if (!$activeShift && $user->isOwner()) {
            // If owner accesses kasir area, look for any open shift
            $activeShift = CashierShift::where('status', 'open')->latest()->first();
        }

        $rooms = Room::all();
        $roomCounts = [
            'available' => $rooms->where('status', 'available')->count(),
            'occupied' => $rooms->where('status', 'occupied')->count(),
            'cleaning' => $rooms->where('status', 'cleaning')->count(),
            'blocked' => $rooms->where('status', 'blocked')->count(),
        ];

        $pendingBookingsCount = Booking::where('status', 'pending')->count();
        $standbyCompanionsCount = Companion::where('status', 'standby')->count();
        $inRoomCompanionsCount = Companion::where('status', 'in_room')->count();

        $todaySales = Sale::whereDate('created_at', Carbon::today())->get();
        $todayRevenue = $todaySales->sum('grand_total');
        $todayTransactionsCount = $todaySales->count();

        $recentSales = Sale::with(['room', 'user'])->latest()->take(5)->get();

        return view('kasir.dashboard', compact(
            'activeShift', 'rooms', 'roomCounts', 'pendingBookingsCount',
            'standbyCompanionsCount', 'inRoomCompanionsCount', 'todayRevenue',
            'todayTransactionsCount', 'recentSales'
        ));
    }

    // Monitoring Status Room (Real-time Grid)
    public function roomsGrid()
    {
        $rooms = Room::with(['companions', 'activeBooking'])->orderBy('room_number')->get();
        $availableCompanions = Companion::where('status', 'standby')->get();
        $products = Product::active()->where('stock', '>', 0)->get()->groupBy('category');
        $packages = Package::active()->get();

        return view('kasir.rooms_grid', compact('rooms', 'availableCompanions', 'products', 'packages'));
    }

    // Walk-in Check-in
    public function checkInWalkIn(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'package_id' => 'nullable|exists:packages,id',
            'duration_hours' => 'required|integer|min:1|max:12',
            'companion_ids' => 'nullable|array',
            'companion_ids.*' => 'exists:companions,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        if ($room->status !== 'available') {
            return back()->with('error', 'Kamar tidak dalam status Available!');
        }

        $now = Carbon::now();
        $endTime = (clone $now)->addHours((int) $validated['duration_hours']);
        $package = !empty($validated['package_id']) ? Package::find($validated['package_id']) : null;

        $roomCharge = $package ? $package->price : ($room->price_per_hour * (int) $validated['duration_hours']);
        $bookingCode = 'WI-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        DB::transaction(function () use ($validated, $room, $package, $now, $endTime, $roomCharge, $bookingCode, $request) {
            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? '-',
                'user_id' => Auth::id(),
                'room_id' => $room->id,
                'package_id' => $package ? $package->id : null,
                'start_time' => $now,
                'end_time' => $endTime,
                'duration_hours' => (int) $validated['duration_hours'],
                'booking_type' => 'walk_in',
                'status' => 'confirmed',
                'include_lc' => !empty($validated['companion_ids']),
                'lc_qty' => !empty($validated['companion_ids']) ? count($validated['companion_ids']) : 0,
                'lc_hours' => !empty($validated['companion_ids']) ? (int) $validated['duration_hours'] : 0,
                'room_charge' => $roomCharge,
                'lc_charge' => 0,
                'preorder_total' => 0,
                'total_estimated_price' => $roomCharge,
                'whatsapp_sent' => false,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Assign Companions
            if (!empty($validated['companion_ids'])) {
                $totalLcCharge = 0;
                foreach ($validated['companion_ids'] as $compId) {
                    $comp = Companion::find($compId);
                    if ($comp) {
                        $subtotal = $comp->price_per_hour * (int) $validated['duration_hours'];
                        $totalLcCharge += $subtotal;
                        BookingCompanion::create([
                            'booking_id' => $booking->id,
                            'companion_id' => $comp->id,
                            'hours' => (int) $validated['duration_hours'],
                            'price_per_hour' => $comp->price_per_hour,
                            'subtotal' => $subtotal,
                            'status' => 'active',
                        ]);
                        $comp->update([
                            'status' => 'in_room',
                            'current_room_id' => $room->id,
                        ]);
                    }
                }
                $booking->update([
                    'lc_charge' => $totalLcCharge,
                    'total_estimated_price' => $booking->total_estimated_price + $totalLcCharge,
                ]);
            }

            // Update Room Status
            $room->update(['status' => 'occupied']);
        });

        return back()->with('success', "Kamar {$room->room_number} berhasil di-check in!");
    }

    // Add F&B Order to Room
    public function orderToRoom(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        $booking = Booking::where('room_id', $room->id)->where('status', 'confirmed')->latest()->first();

        if (!$booking) {
            return back()->with('error', 'Tidak ditemukan booking aktif di kamar ini!');
        }

        DB::transaction(function () use ($validated, $booking) {
            $product = Product::where('id', $validated['product_id'])->lockForUpdate()->firstOrFail();

            if ($product->stock < $validated['quantity']) {
                throw new \Exception("Stok {$product->name} tidak mencukupi! Sisa stok: {$product->stock}");
            }

            $subtotal = $product->selling_price * $validated['quantity'];

            BookingItem::create([
                'booking_id' => $booking->id,
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'price' => $product->selling_price,
                'subtotal' => $subtotal,
            ]);

            // Deduct stock immediately
            $product->decrement('stock', $validated['quantity']);

            // Update booking preorder/total
            $booking->increment('preorder_total', $subtotal);
            $booking->increment('total_estimated_price', $subtotal);
        });

        return back()->with('success', 'Pesanan F&B berhasil ditambahkan ke kamar!');
    }

    // Assign LC to Room
    public function assignCompanionToRoom(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'companion_id' => 'required|exists:companions,id',
            'hours' => 'required|numeric|min:1|max:12',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        $companion = Companion::findOrFail($validated['companion_id']);
        $booking = Booking::where('room_id', $room->id)->where('status', 'confirmed')->latest()->first();

        if (!$booking) {
            return back()->with('error', 'Kamar tidak memiliki sesi aktif!');
        }

        if ($companion->status !== 'standby') {
            return back()->with('error', 'Pemandu lagu ini tidak dalam status Standby!');
        }

        DB::transaction(function () use ($booking, $companion, $room, $validated) {
            $subtotal = $companion->price_per_hour * $validated['hours'];

            BookingCompanion::create([
                'booking_id' => $booking->id,
                'companion_id' => $companion->id,
                'hours' => $validated['hours'],
                'price_per_hour' => $companion->price_per_hour,
                'subtotal' => $subtotal,
                'status' => 'active',
            ]);

            $companion->update([
                'status' => 'in_room',
                'current_room_id' => $room->id,
            ]);

            $booking->increment('lc_charge', $subtotal);
            $booking->increment('total_estimated_price', $subtotal);
            $booking->update(['include_lc' => true]);
        });

        return back()->with('success', "LC {$companion->stage_name} berhasil ditugaskan ke kamar {$room->room_number}!");
    }

    // Extend Room Duration
    public function extendRoom(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'extra_hours' => 'required|integer|min:1|max:6',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        $booking = Booking::where('room_id', $room->id)->where('status', 'confirmed')->latest()->first();

        if (!$booking) {
            return back()->with('error', 'Tidak ada booking aktif untuk kamar ini.');
        }

        $extraCharge = $room->price_per_hour * $validated['extra_hours'];
        $newEndTime = $booking->end_time ? $booking->end_time->addHours($validated['extra_hours']) : Carbon::now()->addHours($validated['extra_hours']);

        $booking->update([
            'duration_hours' => $booking->duration_hours + $validated['extra_hours'],
            'end_time' => $newEndTime,
            'room_charge' => $booking->room_charge + $extraCharge,
            'total_estimated_price' => $booking->total_estimated_price + $extraCharge,
        ]);

        return back()->with('success', "Kamar {$room->room_number} berhasil ditambah {$validated['extra_hours']} jam!");
    }

    // Set Room Status (e.g. Cleaning -> Available)
    public function updateRoomStatus(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $status = $request->validate(['status' => 'required|in:available,cleaning,occupied,blocked'])['status'];

        $room->update(['status' => $status]);

        if ($status === 'available') {
            // Release any remaining companions linked to this room
            Companion::where('current_room_id', $room->id)->update([
                'status' => 'standby',
                'current_room_id' => null,
            ]);
        }

        return back()->with('success', "Status kamar {$room->room_number} diubah menjadi " . ucfirst($status));
    }

    // Live Companion Tracker
    public function companionTracker()
    {
        $companions = Companion::with('currentRoom')->orderBy('status')->orderBy('stage_name')->get();
        $rooms = Room::where('status', 'occupied')->get();

        return view('kasir.companions', compact('companions', 'rooms'));
    }

    public function updateCompanionStatus(Request $request, $id)
    {
        $companion = Companion::findOrFail($id);
        $status = $request->validate([
            'status' => 'required|in:standby,in_room,off',
            'current_room_id' => 'nullable|exists:rooms,id',
        ]);

        $updateData = ['status' => $status['status']];
        if ($status['status'] !== 'in_room') {
            $updateData['current_room_id'] = null;
        } else {
            $updateData['current_room_id'] = $status['current_room_id'] ?? null;
        }

        $companion->update($updateData);

        return back()->with('success', "Status LC {$companion->stage_name} berhasil diperbarui menjadi " . strtoupper($status['status']));
    }

    // Manajemen Reservasi
    public function reservations(Request $request)
    {
        $status = $request->query('status', 'all');
        $query = Booking::with(['room', 'package', 'companions.companion', 'items.product'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(15)->withQueryString();
        $counts = [
            'all' => Booking::count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return view('kasir.reservations', compact('bookings', 'status', 'counts'));
    }

    public function confirmReservation($id)
    {
        $booking = Booking::with(['room', 'companions.companion'])->findOrFail($id);

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah tidak berstatus pending!');
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'confirmed']);
            $booking->room->update(['status' => 'occupied']);

            // Set assigned companions to in_room
            foreach ($booking->companions as $bComp) {
                if ($bComp->companion) {
                    $bComp->companion->update([
                        'status' => 'in_room',
                        'current_room_id' => $booking->room_id,
                    ]);
                    $bComp->update(['status' => 'active']);
                }
            }
        });

        return back()->with('success', "Booking {$booking->booking_code} dikonfirmasi! Kamar {$booking->room->room_number} aktif.");
    }

    public function cancelReservation($id)
    {
        $booking = Booking::with('room')->findOrFail($id);
        $booking->update(['status' => 'cancelled']);

        return back()->with('success', "Booking {$booking->booking_code} telah dibatalkan.");
    }

    // Point of Sale (POS)
    public function pos(Request $request)
    {
        $selectedBookingId = $request->query('booking_id');
        $selectedRoomId = $request->query('room_id');

        $activeBooking = null;
        if ($selectedBookingId) {
            $activeBooking = Booking::with(['room', 'package.items.product', 'items.product', 'companions.companion'])->find($selectedBookingId);
        } elseif ($selectedRoomId) {
            $activeBooking = Booking::with(['room', 'package.items.product', 'items.product', 'companions.companion'])
                ->where('room_id', $selectedRoomId)
                ->where('status', 'confirmed')
                ->latest()
                ->first();
        }

        $occupiedRooms = Room::where('status', 'occupied')->get();
        $products = Product::active()->where('stock', '>', 0)->orderBy('category')->get();
        $companions = Companion::all();
        $allRooms = Room::all();

        return view('kasir.pos', compact('activeBooking', 'occupiedRooms', 'products', 'companions', 'allRooms'));
    }

    // Process POS Checkout (Atomic Transaction & Stock Deduct)
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'nullable|exists:bookings,id',
            'room_id' => 'nullable|exists:rooms,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'total_room' => 'required|numeric|min:0',
            'total_fnb' => 'required|numeric|min:0',
            'total_service' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'change_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,qris,debit,credit,complimentary',
            'is_complimentary' => 'nullable|boolean',
            'complimentary_reason' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|in:room,fnb,service',
            'items.*.item_id' => 'nullable|integer',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.purchase_price' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $activeShift = CashierShift::where('user_id', $user->id)->where('status', 'open')->latest()->first();

        // Enforce DB::transaction with pessimistic locking for concurrency and zero-race stock deduction
        $sale = DB::transaction(function () use ($validated, $user, $activeShift) {
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'booking_id' => $validated['booking_id'] ?? null,
                'room_id' => $validated['room_id'] ?? null,
                'user_id' => $user->id,
                'cashier_shift_id' => $activeShift ? $activeShift->id : null,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'total_room' => $validated['total_room'],
                'total_fnb' => $validated['total_fnb'],
                'total_service' => $validated['total_service'],
                'discount' => $validated['discount'],
                'grand_total' => $validated['grand_total'],
                'paid_amount' => $validated['paid_amount'],
                'change_amount' => $validated['change_amount'],
                'payment_method' => $validated['payment_method'],
                'is_complimentary' => !empty($validated['is_complimentary']),
                'complimentary_reason' => $validated['complimentary_reason'] ?? null,
                'status' => 'paid',
            ]);

            // Process Sale Items & Stock deduction
            foreach ($validated['items'] as $item) {
                $purchasePrice = $item['purchase_price'] ?? 0;

                if ($item['item_type'] === 'fnb' && !empty($item['item_id'])) {
                    $product = Product::where('id', $item['item_id'])->lockForUpdate()->first();
                    if ($product) {
                        $purchasePrice = $product->purchase_price;
                        // Deduct stock if not already deducted in pre-order
                        if (empty($validated['booking_id'])) {
                            if ($product->stock < $item['quantity']) {
                                throw new \Exception("Stok {$product->name} tidak cukup! Tersisa: {$product->stock}");
                            }
                            $product->decrement('stock', (int) $item['quantity']);
                        }
                    }
                } elseif ($item['item_type'] === 'service' && !empty($item['item_id'])) {
                    $companion = Companion::find($item['item_id']);
                    if ($companion) {
                        // Commission cut: e.g. 70% to LC
                        $purchasePrice = ($companion->price_per_hour * ($companion->commission_rate / 100));
                        $companion->increment('total_hours_served', $item['quantity']);
                        // Free companion
                        $companion->update([
                            'status' => 'standby',
                            'current_room_id' => null,
                        ]);
                    }
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'item_type' => $item['item_type'],
                    'item_id' => $item['item_id'] ?? null,
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'purchase_price' => $purchasePrice,
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // Update Shift Cash totals
            if ($activeShift) {
                if ($validated['payment_method'] === 'cash') {
                    $activeShift->increment('total_sales_cash', $validated['grand_total']);
                } elseif ($validated['payment_method'] !== 'complimentary') {
                    $activeShift->increment('total_sales_noncash', $validated['grand_total']);
                }
            }

            // Close Booking & Set Room to Cleaning
            if (!empty($validated['booking_id'])) {
                $booking = Booking::find($validated['booking_id']);
                if ($booking) {
                    $booking->update(['status' => 'completed']);
                    if ($booking->room) {
                        $booking->room->update(['status' => 'cleaning']);
                    }
                }
            } elseif (!empty($validated['room_id'])) {
                $room = Room::find($validated['room_id']);
                if ($room) {
                    $room->update(['status' => 'cleaning']);
                    Companion::where('current_room_id', $room->id)->update([
                        'status' => 'standby',
                        'current_room_id' => null,
                    ]);
                }
            }

            return $sale;
        });

        return redirect()->route('kasir.thermalReceipt', $sale->id)
            ->with('success', "Transaksi berhasil disimpan! No Invoice: {$sale->invoice_number}");
    }

    // Thermal Receipt (58mm/80mm Printable Slip)
    public function thermalReceipt($id)
    {
        $sale = Sale::with(['items', 'user', 'room'])->findOrFail($id);
        return view('kasir.thermal_receipt', compact('sale'));
    }

    // Shift Management
    public function shifts()
    {
        $user = Auth::user();
        $activeShift = CashierShift::where('user_id', $user->id)->where('status', 'open')->latest()->first();
        $recentShifts = CashierShift::with('user')->latest()->take(10)->get();

        return view('kasir.shifts', compact('activeShift', 'recentShifts'));
    }

    public function startShift(Request $request)
    {
        $validated = $request->validate([
            'shift_name' => 'required|string|max:100',
            'starting_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Check if there is already an open shift
        $existing = CashierShift::where('user_id', $user->id)->where('status', 'open')->first();
        if ($existing) {
            return back()->with('error', 'Anda masih memiliki shift yang berstatus Open!');
        }

        CashierShift::create([
            'user_id' => $user->id,
            'shift_name' => $validated['shift_name'],
            'start_time' => Carbon::now(),
            'starting_cash' => $validated['starting_cash'],
            'total_sales_cash' => 0,
            'total_sales_noncash' => 0,
            'status' => 'open',
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Shift kasir berhasil dibuka! Selamat bertugas.');
    }

    public function recordCashMovement(Request $request)
    {
        $validated = $request->validate([
            'cashier_shift_id' => 'required|exists:cashier_shifts,id',
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string|max:255',
        ]);

        CashMovement::create([
            'cashier_shift_id' => $validated['cashier_shift_id'],
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Mutasi kas masuk/keluar berhasil dicatat.');
    }

    public function closeShift(Request $request, $id)
    {
        $shift = CashierShift::findOrFail($id);
        $validated = $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $expectedCash = $shift->expected_cash;
        $actualCash = (float) $validated['actual_cash'];
        $difference = $actualCash - $expectedCash;

        $shift->update([
            'end_time' => Carbon::now(),
            'ending_cash' => $expectedCash,
            'actual_cash' => $actualCash,
            'difference' => $difference,
            'status' => 'closed',
            'notes' => ($shift->notes ? $shift->notes . " | " : "") . ($validated['notes'] ?? ''),
        ]);

        return back()->with('success', "Shift berhasil ditutup! Selisih kas: Rp " . number_format($difference, 0, ',', '.'));
    }
}
