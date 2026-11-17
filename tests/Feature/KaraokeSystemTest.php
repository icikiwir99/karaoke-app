<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\CashierShift;
use App\Models\Companion;
use App\Models\Product;
use App\Models\Room;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KaraokeSystemTest extends TestCase
{
    public function test_customer_public_pages_load_successfully(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/rooms')->assertStatus(200);
        $this->get('/menu')->assertStatus(200);
        $this->get('/song-finder')->assertStatus(200);
        $this->get('/promos')->assertStatus(200);
        $this->get('/reservation')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
    }

    public function test_customer_can_create_reservation_with_lc_and_preorder(): void
    {
        $room = Room::where('status', 'available')->firstOrFail();
        $product = Product::active()->firstOrFail();
        $companion = Companion::where('status', 'standby')->firstOrFail();

        $postData = [
            'customer_name' => 'Ahmad Dani',
            'customer_phone' => '081299998888',
            'room_id' => $room->id,
            'start_date' => date('Y-m-d'),
            'start_hour' => '19:00',
            'duration_hours' => 2,
            'include_lc' => 1,
            'lc_qty' => 1,
            'lc_hours' => 2,
            'selected_companions' => [$companion->id],
            'preorder_products' => [
                $product->id => 2,
            ],
            'notes' => 'Tolong mic disiapkan 3 buah.',
        ];

        $response = $this->post('/reservation', $postData);
        $response->assertStatus(302);

        $booking = Booking::where('customer_phone', '081299998888')->latest()->first();
        $this->assertNotNull($booking);
        $this->assertEquals('pending', $booking->status);
        $this->assertEquals(2, $booking->duration_hours);
        $this->assertEquals(1, $booking->items()->count());
        $this->assertEquals(1, $booking->companions()->count());
    }

    public function test_rbac_security_prevents_unauthorized_access(): void
    {
        // Unauthenticated access to kasir or owner area must redirect to login
        $this->get('/kasir/dashboard')->assertRedirect('/login');
        $this->get('/owner/dashboard')->assertRedirect('/login');

        // Customer user accessing owner area must be denied (403)
        $customer = User::where('role', 'customer')->first();
        if ($customer) {
            $this->actingAs($customer)->get('/owner/dashboard')->assertStatus(403);
            $this->actingAs($customer)->get('/kasir/dashboard')->assertStatus(403);
        }
    }

    public function test_kasir_can_access_operational_dashboard_and_rooms_grid(): void
    {
        $kasir = User::where('role', 'kasir')->firstOrFail();

        $this->actingAs($kasir)->get('/kasir/dashboard')->assertStatus(200);
        $this->actingAs($kasir)->get('/kasir/rooms')->assertStatus(200);
        $this->actingAs($kasir)->get('/kasir/companions')->assertStatus(200);
        $this->actingAs($kasir)->get('/kasir/reservations')->assertStatus(200);
        $this->actingAs($kasir)->get('/kasir/pos')->assertStatus(200);
        $this->actingAs($kasir)->get('/kasir/shifts')->assertStatus(200);
    }

    public function test_atomic_pos_checkout_deducts_stock_and_creates_thermal_receipt(): void
    {
        $kasir = User::where('role', 'kasir')->firstOrFail();
        $room = Room::where('status', 'available')->firstOrFail();
        $product = Product::where('stock', '>', 5)->firstOrFail();
        $initialStock = $product->stock;

        $checkoutData = [
            'room_id' => $room->id,
            'customer_name' => 'Bapak Test Checkout',
            'customer_phone' => '081255556666',
            'total_room' => 170000,
            'total_fnb' => $product->selling_price * 2,
            'total_service' => 0,
            'discount' => 10000,
            'grand_total' => 170000 + ($product->selling_price * 2) - 10000,
            'paid_amount' => 170000 + ($product->selling_price * 2) - 10000 + 20000,
            'change_amount' => 20000,
            'payment_method' => 'cash',
            'items' => [
                [
                    'item_type' => 'room',
                    'item_id' => $room->id,
                    'item_name' => "Sewa {$room->room_number} x 2 Jam",
                    'quantity' => 2,
                    'purchase_price' => 0,
                    'price' => $room->price_per_hour,
                    'subtotal' => 170000,
                ],
                [
                    'item_type' => 'fnb',
                    'item_id' => $product->id,
                    'item_name' => $product->name,
                    'quantity' => 2,
                    'purchase_price' => $product->purchase_price,
                    'price' => $product->selling_price,
                    'subtotal' => $product->selling_price * 2,
                ],
            ],
        ];

        $response = $this->actingAs($kasir)->post('/kasir/checkout', $checkoutData);
        $response->assertStatus(302);

        // Verify product stock was decremented
        $product->refresh();
        $this->assertEquals($initialStock - 2, $product->stock);

        // Verify sale record created
        $sale = Sale::where('customer_name', 'Bapak Test Checkout')->latest()->first();
        $this->assertNotNull($sale);
        $this->assertEquals('paid', $sale->status);

        // Verify thermal receipt loads
        $this->actingAs($kasir)->get("/kasir/receipt/{$sale->id}")->assertStatus(200);
    }

    public function test_owner_can_access_executive_dashboard_and_hold_room(): void
    {
        $owner = User::where('role', 'owner')->firstOrFail();

        $this->actingAs($owner)->get('/owner/dashboard')->assertStatus(200);
        $this->actingAs($owner)->get('/owner/products')->assertStatus(200);
        $this->actingAs($owner)->get('/owner/rooms')->assertStatus(200);
        $this->actingAs($owner)->get('/owner/packages')->assertStatus(200);
        $this->actingAs($owner)->get('/owner/companions')->assertStatus(200);
        $this->actingAs($owner)->get('/owner/reports')->assertStatus(200);
        $this->actingAs($owner)->get('/owner/users')->assertStatus(200);

        // Test Owner Override: Block / Hold room
        $room = Room::firstOrFail();
        $response = $this->actingAs($owner)->post("/owner/rooms/{$room->id}/block", [
            'action' => 'block',
            'block_reason' => 'VIP Maintenance by Owner',
        ]);
        $response->assertStatus(302);

        $room->refresh();
        $this->assertEquals('blocked', $room->status);
        $this->assertEquals('VIP Maintenance by Owner', $room->block_reason);
    }
}
