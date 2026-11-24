<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingCompanion;
use App\Models\BookingItem;
use App\Models\Companion;
use App\Models\Package;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Room;
use App\Models\Song;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $rooms = Room::all();
        $promos = Promo::active()->get();
        $packages = Package::with(['items.product'])->active()->get();
        $featuredAlcohol = Product::active()->alcohol()->take(4)->get();
        $featuredSongs = Song::where('is_favorite', true)->take(6)->get();

        return view('customer.index', compact('rooms', 'promos', 'packages', 'featuredAlcohol', 'featuredSongs'));
    }

    public function rooms()
    {
        $rooms = Room::all()->groupBy('type');
        $promos = Promo::active()->get();
        return view('customer.rooms', compact('rooms', 'promos'));
    }

    public function menu(Request $request)
    {
        $category = $request->query('category', 'all');

        $query = Product::active();
        if ($category !== 'all' && in_array($category, ['food', 'softdrink', 'alcohol'])) {
            $query->where('category', $category);
        }

        $search = $request->query('q');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('category')->orderBy('name')->get();
        return view('customer.menu', compact('products', 'category', 'search'));
    }

    public function songFinder(Request $request)
    {
        $search = $request->query('q');
        $genre = $request->query('genre');
        $language = $request->query('language');

        $query = Song::query();

        if (!empty($search)) {
            $query->search($search);
        }

        if (!empty($genre)) {
            $query->where('genre', 'like', "%{$genre}%");
        }

        if (!empty($language)) {
            $query->where('language', $language);
        }

        $songs = $query->orderByDesc('is_favorite')->orderByDesc('views_count')->paginate(12)->withQueryString();

        $genres = Song::select('genre')->distinct()->pluck('genre');
        $languages = Song::select('language')->distinct()->pluck('language');

        return view('customer.songs', compact('songs', 'search', 'genre', 'language', 'genres', 'languages'));
    }

    public function promos()
    {
        $promos = Promo::active()->get();
        return view('customer.promos', compact('promos'));
    }

    // FIX: tarif default LC diambil dari database (rata-rata tarif LC aktif), bukan angka hardcode 175000
    private function defaultLcRate(): int
    {
        $avg = Companion::where('status', '!=', 'off')->avg('price_per_hour');
        return $avg ? (int) round($avg) : 175000; // 175000 hanya cadangan jika tabel LC kosong
    }

    public function reservationForm(Request $request)
    {
        $selectedRoomId = $request->query('room_id');
        $selectedPackageId = $request->query('package_id');

        $rooms = Room::where('status', '!=', 'blocked')->get();
        $packages = Package::with(['items.product'])->active()->get();
        $companions = Companion::where('status', '!=', 'off')->get();
        $products = Product::active()->where('stock', '>', 0)->get()->groupBy('category');
        $defaultLcRate = $this->defaultLcRate(); // FIX: dikirim ke view untuk perhitungan di browser

        return view('customer.reservation', compact(
            'rooms', 'packages', 'companions', 'products',
            'selectedRoomId', 'selectedPackageId', 'defaultLcRate'
        ));
    }

    public function storeReservation(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:30',
            'room_id' => 'required|exists:rooms,id',
            'package_id' => 'nullable|exists:packages,id',
            'start_date' => 'required|date',
            'start_hour' => 'required|string',
            'duration_hours' => 'required|integer|min:1|max:12',
            'include_lc' => 'nullable|boolean',
            'lc_qty' => 'nullable|integer|min:0|max:10',
            'lc_hours' => 'nullable|integer|min:0|max:12',
            'selected_companions' => 'nullable|array|max:10',
            'selected_companions.*' => 'exists:companions,id',
            'preorder_products' => 'nullable|array',
            'preorder_products.*' => 'nullable|integer|min:0|max:999', // FIX: kuantitas harus angka valid
            'notes' => 'nullable|string|max:1000',
        ]);

        $startDateTime = Carbon::parse($validated['start_date'] . ' ' . $validated['start_hour']);
        $duration = (int) $validated['duration_hours'];
        $endDateTime = (clone $startDateTime)->addHours($duration);

        $room = Room::findOrFail($validated['room_id']);
        $package = !empty($validated['package_id']) ? Package::find($validated['package_id']) : null;

        // Harga kamar / paket
        $roomCharge = $package ? $package->price : $room->price_per_hour * $duration;

        // ===== FIX: Perhitungan LC mengikuti tarif personel yang dipilih + durasi =====
        $includeLc = $request->boolean('include_lc');
        $lcQty = 0;
        $lcHours = 0;
        $lcCharge = 0;
        $selectedCompanions = collect();

        if ($includeLc) {
            // Durasi LC tidak boleh melebihi durasi sewa kamar
            $lcHours = max(1, min((int) ($validated['lc_hours'] ?? $duration), $duration));

            $ids = array_unique($validated['selected_companions'] ?? []);
            if (count($ids) > 0) {
                $selectedCompanions = Companion::whereIn('id', $ids)->where('status', '!=', 'off')->get();
            }

            if ($selectedCompanions->count() > 0) {
                // Ada personel dipilih: jumlah orang = jumlah personel, biaya = jumlah tarif masing-masing x jam
                $lcQty = $selectedCompanions->count();
                if (!$package) {
                    $lcCharge = (float) $selectedCompanions->sum('price_per_hour') * $lcHours;
                }
            } else {
                // Tidak ada personel dipilih: pakai tarif default dari database x jumlah orang x jam
                $lcQty = max(1, (int) ($validated['lc_qty'] ?? 1));
                if (!$package) {
                    $lcCharge = $this->defaultLcRate() * $lcQty * $lcHours;
                }
            }
        }

        $bookingCode = 'BK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        try {
            $booking = DB::transaction(function () use (
                $validated, $startDateTime, $endDateTime, $room, $package,
                $roomCharge, $lcCharge, $includeLc, $lcQty, $lcHours, $selectedCompanions,
                $bookingCode, $request
            ) {
                $booking = Booking::create([
                    'booking_code' => $bookingCode,
                    'customer_name' => $validated['customer_name'],
                    'customer_phone' => $validated['customer_phone'],
                    'user_id' => Auth::id(),
                    'room_id' => $room->id,
                    'package_id' => $package ? $package->id : null,
                    'start_time' => $startDateTime,
                    'end_time' => $endDateTime,
                    'duration_hours' => (int) $validated['duration_hours'],
                    'booking_type' => 'online',
                    'status' => 'pending',
                    'include_lc' => $includeLc,
                    'lc_qty' => $lcQty,
                    'lc_hours' => $lcHours,
                    'room_charge' => $roomCharge,
                    'lc_charge' => $lcCharge,
                    'preorder_total' => 0,
                    'total_estimated_price' => $roomCharge + $lcCharge,
                    'whatsapp_sent' => false,
                    'notes' => $validated['notes'] ?? null,
                ]);

                // ===== FIX: Pre-order dengan validasi stok + stok langsung dikurangi =====
                $preorders = array_filter(
                    array_map('intval', $request->input('preorder_products', [])),
                    fn ($q) => $q > 0
                );
                ksort($preorders); // urutan tetap supaya tidak terjadi deadlock antar pelanggan
                $preorderSum = 0;

                foreach ($preorders as $productId => $qty) {
                    // Kunci baris produk: 2 pelanggan tidak bisa mengambil stok yang sama
                    $product = Product::active()->where('id', $productId)->lockForUpdate()->first();
                    if (!$product) {
                        continue;
                    }

                    if ($qty > $product->stock) {
                        throw new \RuntimeException(
                            "Stok {$product->name} tinggal {$product->stock}, tidak cukup untuk pesanan {$qty}."
                        );
                    }

                    $subtotal = $product->selling_price * $qty;
                    $preorderSum += $subtotal;

                    BookingItem::create([
                        'booking_id' => $booking->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'price' => $product->selling_price,
                        'subtotal' => $subtotal,
                    ]);

                    // Stok langsung dikurangi (kasir mengasumsikan "sudah dikurangi saat pre-order")
                    $product->decrement('stock', $qty);
                }

                // Simpan personel LC yang dipilih (tarif & subtotal sesuai durasi)
                foreach ($selectedCompanions as $comp) {
                    $hours = $lcHours > 0 ? $lcHours : 2;
                    BookingCompanion::create([
                        'booking_id' => $booking->id,
                        'companion_id' => $comp->id,
                        'hours' => $hours,
                        'price_per_hour' => $comp->price_per_hour,
                        'subtotal' => $comp->price_per_hour * $hours,
                        'status' => 'assigned',
                    ]);
                }

                $booking->update([
                    'preorder_total' => $preorderSum,
                    'total_estimated_price' => $booking->total_estimated_price + $preorderSum,
                ]);

                return $booking;
            });
        } catch (\RuntimeException $e) {
            // FIX: stok kurang -> kembali ke form dengan pesan jelas (transaksi otomatis dibatalkan)
            return back()->withErrors(['stok' => $e->getMessage()])->withInput();
        }

        return redirect()->route('customer.reservation.success', $booking->id);
    }

    public function reservationSuccess($id)
    {
        $booking = Booking::with(['room', 'package', 'items.product', 'companions.companion'])->findOrFail($id);
        $adminPhone = '081234567890'; // Admin WhatsApp
        $whatsappUrl = $booking->generateWhatsAppUrl($adminPhone);

        return view('customer.reservation_success', compact('booking', 'whatsappUrl'));
    }
}