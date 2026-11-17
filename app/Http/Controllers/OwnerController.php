<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Companion;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\Product;
use App\Models\Room;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class OwnerController extends Controller
{
    // Executive Analytics Dashboard
    public function dashboard()
    {
        // 1. High Level Metrics
        $totalRevenue = Sale::where('status', 'paid')->sum('grand_total');
        $thisMonthRevenue = Sale::where('status', 'paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('grand_total');
        $todayRevenue = Sale::where('status', 'paid')
            ->whereDate('created_at', Carbon::today())
            ->sum('grand_total');

        $totalTransactions = Sale::where('status', 'paid')->count();
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();

        // 2. Low Stock Alerts (Stock Control & Alert)
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->orderBy('stock')->get();

        // 3. Peak Hours Analytics (0-23 hour distribution)
        $peakHoursRaw = Sale::select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as total_sales'), DB::raw('SUM(grand_total) as revenue'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $hoursLabels = [];
        $hoursData = [];
        for ($h = 10; $h <= 24; $h++) {
            $formattedHour = sprintf('%02d:00', $h % 24);
            $hoursLabels[] = $formattedHour;
            $found = $peakHoursRaw->firstWhere('hour', $h % 24);
            $hoursData[] = $found ? (int) $found->total_sales : 0;
        }

        // 4. Room Popularity & Stats
        $roomStats = Room::withCount(['sales as total_bookings'])
            ->withSum('sales as total_revenue', 'grand_total')
            ->orderByDesc('total_bookings')
            ->get();

        // 5. Daily Revenue Last 7 Days
        $dailyDates = [];
        $dailyRevenues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyDates[] = $date->format('d M');
            $rev = Sale::where('status', 'paid')->whereDate('created_at', $date)->sum('grand_total');
            $dailyRevenues[] = (float) $rev;
        }

        // 6. Top Alcohol Profit Margins
        $topAlcoholMargin = Product::alcohol()
            ->select('*', DB::raw('(selling_price - purchase_price) as margin_amount'))
            ->orderByDesc('margin_amount')
            ->take(5)
            ->get();

        return view('owner.dashboard', compact(
            'totalRevenue', 'thisMonthRevenue', 'todayRevenue',
            'totalTransactions', 'totalRooms', 'occupiedRooms',
            'lowStockProducts', 'hoursLabels', 'hoursData',
            'roomStats', 'dailyDates', 'dailyRevenues', 'topAlcoholMargin'
        ));
    }

    // Master Data Products (F&B & Alcohol/Miras)
    public function products(Request $request)
    {
        $category = $request->query('category', 'all');
        $query = Product::query();

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where('name', 'like', "%{$q}%");
        }

        if ($request->query('low_stock') === '1') {
            $query->whereColumn('stock', '<=', 'min_stock');
        }

        $products = $query->orderBy('category')->orderBy('name')->paginate(15)->withQueryString();
        $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')->count();

        return view('owner.products', compact('products', 'category', 'lowStockCount'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:food,softdrink,alcohol',
            'unit' => 'required|in:bottle,glass,can,portion,tower,bucket',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'alcohol_content' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        Product::create($validated);

        return back()->with('success', "Produk {$validated['name']} berhasil ditambahkan!");
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:food,softdrink,alcohol',
            'unit' => 'required|in:bottle,glass,can,portion,tower,bucket',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'alcohol_content' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $product->update($validated);

        return back()->with('success', "Data {$product->name} berhasil diperbarui!");
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return back()->with('success', 'Produk berhasil dihapus.');
    }

    // Master Data Rooms
    public function rooms()
    {
        $rooms = Room::with('companions')->orderBy('room_number')->get();
        return view('owner.rooms', compact('rooms'));
    }

    public function storeRoom(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:20|unique:rooms,room_number',
            'name' => 'nullable|string|max:100',
            'type' => 'required|in:small,medium,large,vip',
            'price_per_hour' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'facilities' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        Room::create($validated);
        return back()->with('success', "Kamar {$validated['room_number']} berhasil ditambahkan!");
    }

    public function updateRoom(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $validated = $request->validate([
            'room_number' => 'required|string|max:20|unique:rooms,room_number,' . $room->id,
            'name' => 'nullable|string|max:100',
            'type' => 'required|in:small,medium,large,vip',
            'price_per_hour' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'facilities' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $room->update($validated);
        return back()->with('success', "Kamar {$room->room_number} berhasil diperbarui!");
    }

    // Manual Override / Owner's Privilege: Hold / Block Room
    public function toggleRoomBlock(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $validated = $request->validate([
            'action' => 'required|in:block,unblock',
            'block_reason' => 'nullable|string|max:255',
        ]);

        if ($validated['action'] === 'block') {
            $room->update([
                'status' => 'blocked',
                'block_reason' => $validated['block_reason'] ?? 'Hold by Owner (Maintenance / Private Event)',
            ]);
            return back()->with('success', "Kamar {$room->room_number} berhasil di-BLOKIR (Hold Room)!");
        } else {
            $room->update([
                'status' => 'available',
                'block_reason' => null,
            ]);
            return back()->with('success', "Kamar {$room->room_number} berhasil dibuka kembali (Available)!");
        }
    }

    // Master Data Packages (Bundling)
    public function packages()
    {
        $packages = Package::with(['items.product'])->get();
        $products = Product::active()->get();
        return view('owner.packages', compact('packages', 'products'));
    }

    public function storePackage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'room_type' => 'required|in:small,medium,large,vip',
            'room_hours' => 'required|integer|min:1',
            'include_lc_qty' => 'required|integer|min:0',
            'include_lc_hours' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.product_id' => 'exists:products,id',
            'items.*.quantity' => 'integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $package = Package::create([
                'name' => $validated['name'],
                'room_type' => $validated['room_type'],
                'room_hours' => $validated['room_hours'],
                'include_lc_qty' => $validated['include_lc_qty'],
                'include_lc_hours' => $validated['include_lc_hours'],
                'price' => $validated['price'],
                'description' => $validated['description'] ?? null,
            ]);

            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    if (!empty($item['product_id']) && !empty($item['quantity'])) {
                        PackageItem::create([
                            'package_id' => $package->id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                        ]);
                    }
                }
            }
        });

        return back()->with('success', 'Paket Bundling berhasil dibuat!');
    }

    public function deletePackage($id)
    {
        $package = Package::findOrFail($id);
        $package->delete();
        return back()->with('success', 'Paket Bundling berhasil dihapus.');
    }

    // Companion (LC) Master Data & Performance Report
    public function companions()
    {
        $companions = Companion::withCount('bookingCompanions')->get();
        return view('owner.companions', compact('companions'));
    }

    public function storeCompanion(Request $request)
    {
        $validated = $request->validate([
            'stage_name' => 'required|string|max:100',
            'real_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'price_per_hour' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'skills' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Companion::create($validated);
        return back()->with('success', "Pemandu lagu {$validated['stage_name']} berhasil ditambahkan!");
    }

    public function updateCompanion(Request $request, $id)
    {
        $companion = Companion::findOrFail($id);
        $validated = $request->validate([
            'stage_name' => 'required|string|max:100',
            'real_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'price_per_hour' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'skills' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:standby,in_room,off',
        ]);

        $companion->update($validated);
        return back()->with('success', "Data LC {$companion->stage_name} berhasil diperbarui!");
    }

    // User Management (Kasir & Staff)
    public function users()
    {
        $users = User::all();
        return view('owner.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:customer,kasir,owner',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', "User pegawai {$validated['name']} berhasil dibuat!");
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:customer,kasir,owner',
            'password' => 'nullable|string|min:6',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return back()->with('success', "Akun {$user->name} berhasil diperbarui!");
    }

    // Financial Reports & Product Margins
    public function reports(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::today()->subDays(30)->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());

        $sales = Sale::with(['items', 'user', 'room'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'gross_revenue' => Sale::where('status', 'paid')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->sum('grand_total'),
            'total_discounts' => Sale::where('status', 'paid')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->sum('discount'),
            'total_room_rev' => Sale::where('status', 'paid')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->sum('total_room'),
            'total_fnb_rev' => Sale::where('status', 'paid')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->sum('total_fnb'),
            'total_service_rev' => Sale::where('status', 'paid')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->sum('total_service'),
        ];

        // Alcohol / Miras Margin Report (Showing highest profit margin)
        $alcoholMargins = SaleItem::where('item_type', 'fnb')
            ->whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'paid')
                  ->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
            })
            ->select(
                'item_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('AVG(price) as avg_price'),
                DB::raw('AVG(purchase_price) as avg_cogs'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('SUM(purchase_price * quantity) as total_cost'),
                DB::raw('SUM(subtotal - (purchase_price * quantity)) as total_profit')
            )
            ->groupBy('item_name')
            ->orderByDesc('total_profit')
            ->get();

        return view('owner.reports', compact('sales', 'summary', 'alcoholMargins', 'startDate', 'endDate'));
    }

    // Export Reports to CSV (Excel compatible)
    public function exportCsv(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::today()->subDays(30)->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());

        $sales = Sale::with(['items', 'user', 'room'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest()
            ->get();

        $filename = "Laporan_Penjualan_Karaoke_{$startDate}_sampai_{$endDate}.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($sales) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Microsoft Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'No Invoice',
                'Tanggal',
                'Kasir',
                'Customer',
                'Room',
                'Total Sewa Room',
                'Total F&B / Miras',
                'Total Layanan LC',
                'Diskon',
                'Grand Total',
                'Metode Pembayaran',
                'Complimentary?',
                'Alasan Complimentary'
            ]);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->invoice_number,
                    $sale->created_at->format('Y-m-d H:i'),
                    $sale->user ? $sale->user->name : '-',
                    $sale->customer_name,
                    $sale->room ? $sale->room->room_number : '-',
                    $sale->total_room,
                    $sale->total_fnb,
                    $sale->total_service,
                    $sale->discount,
                    $sale->grand_total,
                    strtoupper($sale->payment_method),
                    $sale->is_complimentary ? 'YA' : 'TIDAK',
                    $sale->complimentary_reason ?? '-'
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
