<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rooms
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->string('name')->nullable();
            $table->enum('type', ['small', 'medium', 'large', 'vip']);
            $table->decimal('price_per_hour', 12, 2);
            $table->integer('capacity');
            $table->enum('status', ['available', 'occupied', 'cleaning', 'blocked'])->default('available');
            $table->text('facilities')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('block_reason')->nullable();
            $table->timestamps();
        });

        // 2. Companions (Pemandu Lagu / LC)
        Schema::create('companions', function (Blueprint $table) {
            $table->id();
            $table->string('stage_name');
            $table->string('real_name')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('price_per_hour', 12, 2)->default(150000);
            $table->decimal('commission_rate', 5, 2)->default(70.00); // 70% commission to LC
            $table->enum('status', ['standby', 'in_room', 'off'])->default('standby');
            $table->foreignId('current_room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->string('photo')->nullable();
            $table->string('gender')->default('female');
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->string('skills')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_hours_served', 8, 2)->default(0);
            $table->timestamps();
        });

        // 3. Products (F&B & Miras)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['food', 'softdrink', 'alcohol']);
            $table->enum('unit', ['bottle', 'glass', 'can', 'portion', 'tower', 'bucket'])->default('bottle');
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2);
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->string('alcohol_content')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Packages (Bundling)
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('room_type', ['small', 'medium', 'large', 'vip']);
            $table->integer('room_hours')->default(2);
            $table->integer('include_lc_qty')->default(0);
            $table->integer('include_lc_hours')->default(0);
            $table->decimal('price', 12, 2);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Package Items
        Schema::create('package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        // 5. Bookings
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->integer('duration_hours')->default(2);
            $table->enum('booking_type', ['online', 'walk_in', 'owner_hold'])->default('online');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->boolean('include_lc')->default(false);
            $table->integer('lc_qty')->default(0);
            $table->integer('lc_hours')->default(0);
            $table->decimal('room_charge', 12, 2)->default(0);
            $table->decimal('lc_charge', 12, 2)->default(0);
            $table->decimal('preorder_total', 12, 2)->default(0);
            $table->decimal('total_estimated_price', 12, 2)->default(0);
            $table->boolean('whatsapp_sent')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Booking Companions
        Schema::create('booking_companions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('companion_id')->constrained('companions')->cascadeOnDelete();
            $table->decimal('hours', 5, 2)->default(1);
            $table->decimal('price_per_hour', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->enum('status', ['assigned', 'active', 'completed'])->default('assigned');
            $table->timestamps();
        });

        // Booking Pre-order Items
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        // 6. Cashier Shifts & Cash Movements
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('shift_name')->default('Shift Reguler');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->decimal('starting_cash', 12, 2)->default(0);
            $table->decimal('ending_cash', 12, 2)->nullable();
            $table->decimal('actual_cash', 12, 2)->nullable();
            $table->decimal('difference', 12, 2)->nullable();
            $table->decimal('total_sales_cash', 12, 2)->default(0);
            $table->decimal('total_sales_noncash', 12, 2)->default(0);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_shift_id')->constrained('cashier_shifts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['in', 'out']);
            $table->decimal('amount', 12, 2);
            $table->string('reason');
            $table->timestamps();
        });

        // 7. Sales & Sale Items
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users'); // Kasir
            $table->foreignId('cashier_shift_id')->nullable()->constrained('cashier_shifts')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->decimal('total_room', 12, 2)->default(0);
            $table->decimal('total_fnb', 12, 2)->default(0);
            $table->decimal('total_service', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('change_amount', 12, 2)->default(0);
            $table->enum('payment_method', ['cash', 'qris', 'debit', 'credit', 'complimentary'])->default('cash');
            $table->boolean('is_complimentary')->default(false);
            $table->string('complimentary_reason')->nullable();
            $table->enum('status', ['paid', 'refunded', 'void'])->default('paid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->enum('item_type', ['room', 'fnb', 'service']);
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->decimal('quantity', 8, 2)->default(1);
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        // 8. Songs (Song Finder)
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('artist');
            $table->string('genre')->default('Pop');
            $table->string('language')->default('Indonesia');
            $table->text('lyrics_snippet')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->integer('views_count')->default(0);
            $table->timestamps();
        });

        // 9. Promos & FAQ
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('badge')->nullable();
            $table->string('discount_text')->nullable();
            $table->text('description');
            $table->text('terms')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
        Schema::dropIfExists('songs');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('cash_movements');
        Schema::dropIfExists('cashier_shifts');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('booking_companions');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('package_items');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('products');
        Schema::dropIfExists('companions');
        Schema::dropIfExists('rooms');
    }
};
