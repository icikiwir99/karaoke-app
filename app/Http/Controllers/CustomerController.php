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

    public function reservationForm(Request $request)
    {
        $selectedRoomId = $request->query('room_id');
        $selectedPackageId = $request->query('package_id');

        $rooms = Room::where('status', '!=', 'blocked')->get();
        $packages = Package::with(['items.product'])->active()->get();
        $companions = Companion::where('status', '!=', 'off')->get();
        $products = Product::active()->where('stock', '>', 0)->get()->groupBy('category');

        return view('customer.reservation', compact('rooms', 'packages', 'companions', 'products', 'selectedRoomId', 'selectedPackageId'));
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
            'selected_companions' => 'nullable|array',
            'selected_companions.*' => 'exists:companions,id',
            'preorder_products' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ]);

        $startDateTime = Carbon::parse($validated['start_date'] . ' ' . $validated['start_hour']);
        $endDateTime = (clone $startDateTime)->addHours((int) $validated['duration_hours']);

        $room = Room::findOrFail($validated['room_id']);
        $package = !empty($validated['package_id']) ? Package::find($validated['package_id']) : null;

        // Calculate Pricing
        $roomCharge = 0;
        $lcCharge = 0;
        $preorderTotal = 0;

        if ($package) {
            $totalEstimated = $package->price;
            $roomCharge = $package->price;
        } else {
            $roomCharge = $room->price_per_hour * (int) $validated['duration_hours'];
            $totalEstimated = $roomCharge;
        }

        // Add-on LC calculation
        $includeLc = $request->boolean('include_lc');
        $lcQty = $includeLc ? (int) ($validated['lc_qty'] ?? 1) : 0;
        $lcHours = $includeLc ? (int) ($validated['lc_hours'] ?? $validated['duration_hours']) : 0;

        if ($includeLc && $lcQty > 0 && !$package) {
            // Default companion rate average Rp 175.000 / hour
            $lcRate = 175000;
            $lcCharge = $lcRate * $lcQty * $lcHours;
            $totalEstimated += $lcCharge;
        }

        $bookingCode = 'BK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $booking = DB::transaction(function () use (
            $validated, $startDateTime, $endDateTime, $room, $package,
            $roomCharge, $lcCharge, $includeLc, $lcQty, $lcHours, $bookingCode, $request
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

            // Save Pre-orders if any
            $preorders = $request->input('preorder_products', []);
            $preorderSum = 0;
            foreach ($preorders as $productId => $qty) {
                $qty = (int) $qty;
                if ($qty > 0) {
                    $product = Product::find($productId);
                    if ($product) {
                        $subtotal = $product->selling_price * $qty;
                        $preorderSum += $subtotal;
                        BookingItem::create([
                            'booking_id' => $booking->id,
                            'product_id' => $product->id,
                            'quantity' => $qty,
                            'price' => $product->selling_price,
                            'subtotal' => $subtotal,
                        ]);
                    }
                }
            }

            // Save Selected Companions if specified
            $selectedCompanions = $request->input('selected_companions', []);
            foreach ($selectedCompanions as $companionId) {
                $comp = Companion::find($companionId);
                if ($comp) {
                    BookingCompanion::create([
                        'booking_id' => $booking->id,
                        'companion_id' => $comp->id,
                        'hours' => $lcHours > 0 ? $lcHours : 2,
                        'price_per_hour' => $comp->price_per_hour,
                        'subtotal' => $comp->price_per_hour * ($lcHours > 0 ? $lcHours : 2),
                        'status' => 'assigned',
                    ]);
                }
            }

            $booking->update([
                'preorder_total' => $preorderSum,
                'total_estimated_price' => $booking->total_estimated_price + $preorderSum,
            ]);

            return $booking;
        });

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
