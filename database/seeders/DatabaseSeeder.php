<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingCompanion;
use App\Models\BookingItem;
use App\Models\CashierShift;
use App\Models\Companion;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Room;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Song;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $owner = User::create([
            'name' => 'Bapak Hartono (Owner)',
            'username' => 'owner',
            'email' => 'owner@karaoke.com',
            'phone' => '081122334455',
            'role' => 'owner',
            'password' => Hash::make('password123'),
        ]);

        $kasir1 = User::create([
            'name' => 'Rian Kasir Siang',
            'username' => 'kasir',
            'email' => 'kasir1@karaoke.com',
            'phone' => '081234567890',
            'role' => 'kasir',
            'password' => Hash::make('password123'),
        ]);

        $kasir2 = User::create([
            'name' => 'Dimas Kasir Malam',
            'username' => 'kasir2',
            'email' => 'kasir2@karaoke.com',
            'phone' => '081298765432',
            'role' => 'kasir',
            'password' => Hash::make('password123'),
        ]);

        $customer = User::create([
            'name' => 'Budi Santoso',
            'username' => 'budi',
            'email' => 'budi@gmail.com',
            'phone' => '081377889900',
            'role' => 'customer',
            'password' => Hash::make('password123'),
        ]);

        // 2. Rooms
        $roomsData = [
            [
                'room_number' => 'S-01',
                'name' => 'Neon Glow',
                'type' => 'small',
                'price_per_hour' => 85000,
                'capacity' => 4,
                'status' => 'available',
                'facilities' => '50" 4K Smart TV, BMB Professional Sound, 2 Wireless Mic, Mood Lighting',
                'description' => 'Kamar nyaman dan privat berkapasitas 2-4 orang, cocok untuk hangout santai bersama teman dekat.',
            ],
            [
                'room_number' => 'S-02',
                'name' => 'Cyber Beat',
                'type' => 'small',
                'price_per_hour' => 85000,
                'capacity' => 4,
                'status' => 'occupied',
                'facilities' => '50" 4K Smart TV, BMB Professional Sound, 2 Wireless Mic, Neon Ambient',
                'description' => 'Interior bergaya futuristik cyberpunk dengan akustik ruang prima.',
            ],
            [
                'room_number' => 'S-03',
                'name' => 'Retro Sunset',
                'type' => 'small',
                'price_per_hour' => 85000,
                'capacity' => 4,
                'status' => 'cleaning',
                'facilities' => '50" 4K Smart TV, Yamaha Audio System, 2 Wireless Mic',
                'description' => 'Desain hangat retro dengan sofa empuk berlapis kulit sintetis.',
            ],
            [
                'room_number' => 'M-01',
                'name' => 'Sapphire Lounge',
                'type' => 'medium',
                'price_per_hour' => 140000,
                'capacity' => 8,
                'status' => 'occupied',
                'facilities' => '65" 4K Smart TV, JBL Karaoke Sound, 4 Wireless Mic, Laser Lights, iPad Touch Song Selector',
                'description' => 'Ruangan medium favorit untuk grup 6-8 orang dengan pencahayaan laser dinamis.',
            ],
            [
                'room_number' => 'M-02',
                'name' => 'Ruby Pulse',
                'type' => 'medium',
                'price_per_hour' => 140000,
                'capacity' => 8,
                'status' => 'available',
                'facilities' => '65" 4K Smart TV, JBL Karaoke Sound, 4 Wireless Mic, LED Moving Head',
                'description' => 'Warna merah marun elegan dengan sound system powerful.',
            ],
            [
                'room_number' => 'M-03',
                'name' => 'Emerald Groove',
                'type' => 'medium',
                'price_per_hour' => 140000,
                'capacity' => 8,
                'status' => 'available',
                'facilities' => '65" 4K Smart TV, JBL Sound, 4 Wireless Mic, Strobe FX',
                'description' => 'Nuansa hijau emerald mewah, sangat nyaman untuk bernyanyi berjam-jam.',
            ],
            [
                'room_number' => 'L-01',
                'name' => 'Platinum Arena',
                'type' => 'large',
                'price_per_hour' => 240000,
                'capacity' => 16,
                'status' => 'available',
                'facilities' => '75" 4K Smart TV + Dual Screen, Bose Professional Sound, 4 Wireless Mic, Stage Podium, Smoke Machine',
                'description' => 'Dilengkapi mini stage podium dan efek smoke machine untuk sensasi konser pribadi.',
            ],
            [
                'room_number' => 'L-02',
                'name' => 'Titanium Hall',
                'type' => 'large',
                'price_per_hour' => 240000,
                'capacity' => 16,
                'status' => 'blocked',
                'facilities' => '75" 4K Smart TV, Bose Sound, 4 Wireless Mic, DJ Booth Ready',
                'description' => 'Kamar besar cocok untuk pesta kantor atau ulang tahun keluarga.',
                'block_reason' => 'Perawatan berkala perangkat sound system dan sofa',
            ],
            [
                'room_number' => 'VIP-01',
                'name' => 'The Imperial Suite',
                'type' => 'vip',
                'price_per_hour' => 450000,
                'capacity' => 25,
                'status' => 'occupied',
                'facilities' => '85" 8K QLED TV, Meyer Sound Studio Grade, Private Bar Counter, Private Restroom, Dedicated Waiter, 6 Gold Mic',
                'description' => 'Suite termewah dengan private bar, toilet dalam, dan pelayan pribadi eksklusif.',
            ],
            [
                'room_number' => 'VIP-02',
                'name' => 'Royal Penthouse',
                'type' => 'vip',
                'price_per_hour' => 450000,
                'capacity' => 25,
                'status' => 'available',
                'facilities' => '85" 8K QLED TV, Meyer Sound System, Private Bar Counter, Wine Chiller, Private Restroom',
                'description' => 'Nuansa penthouse mewah dengan pemandangan kota dan fasilitas super VIP.',
            ],
        ];

        $createdRooms = [];
        foreach ($roomsData as $data) {
            $createdRooms[$data['room_number']] = Room::create($data);
        }

        // 3. Companions (Pemandu Lagu / LC)
        $companionsData = [
            [
                'stage_name' => 'Luna',
                'real_name' => 'Siti Maulida',
                'phone' => '081211223301',
                'price_per_hour' => 175000,
                'commission_rate' => 70.00,
                'status' => 'in_room',
                'current_room_id' => $createdRooms['S-02']->id,
                'gender' => 'female',
                'rating' => 4.95,
                'skills' => 'Pop, R&B, Bilingual (Eng/Id), Fun & Cheerful',
                'notes' => 'Top requested companion, sangat ramah dan vokal prima.',
                'total_hours_served' => 124.5,
            ],
            [
                'stage_name' => 'Bella',
                'real_name' => 'Annisa Pratiwi',
                'phone' => '081211223302',
                'price_per_hour' => 175000,
                'commission_rate' => 70.00,
                'status' => 'in_room',
                'current_room_id' => $createdRooms['M-01']->id,
                'gender' => 'female',
                'rating' => 4.90,
                'skills' => 'Dangdut Koplo, Pop Ballad, Energik, Jago Nyanyi',
                'notes' => 'Spesialis pesta ramai dan lagu dangdut koplo hits.',
                'total_hours_served' => 98.0,
            ],
            [
                'stage_name' => 'Sherly',
                'real_name' => 'Sherly Anggraini',
                'phone' => '081211223303',
                'price_per_hour' => 200000,
                'commission_rate' => 70.00,
                'status' => 'in_room',
                'current_room_id' => $createdRooms['VIP-01']->id,
                'gender' => 'female',
                'rating' => 5.00,
                'skills' => 'Mandarin, Classic Pop, Jazz, Fluent Mandarin',
                'notes' => 'Spesialis tamu VIP dan lagu-lagu Mandarin/Klasik.',
                'total_hours_served' => 160.0,
            ],
            [
                'stage_name' => 'Jessica',
                'real_name' => 'Jessica Tan',
                'phone' => '081211223304',
                'price_per_hour' => 200000,
                'commission_rate' => 70.00,
                'status' => 'in_room',
                'current_room_id' => $createdRooms['VIP-01']->id,
                'gender' => 'female',
                'rating' => 4.88,
                'skills' => 'K-Pop, Western Pop, EDM, Modeling Background',
                'notes' => 'Penampilan elegan, humble, pandai mencairkan suasana.',
                'total_hours_served' => 110.0,
            ],
            [
                'stage_name' => 'Cindy',
                'real_name' => 'Cindy Novita',
                'phone' => '081211223305',
                'price_per_hour' => 150000,
                'commission_rate' => 70.00,
                'status' => 'standby',
                'current_room_id' => null,
                'gender' => 'female',
                'rating' => 4.80,
                'skills' => 'Indie Pop, Rock Ballad, Easy Going',
                'notes' => 'Standby di lounge LC, siap ditugaskan.',
                'total_hours_served' => 72.0,
            ],
            [
                'stage_name' => 'Natasha',
                'real_name' => 'Natasha Wijaya',
                'phone' => '081211223306',
                'price_per_hour' => 175000,
                'commission_rate' => 70.00,
                'status' => 'standby',
                'current_room_id' => null,
                'gender' => 'female',
                'rating' => 4.92,
                'skills' => 'Top 40 Hits, R&B, Sweet Voice, Host Karaoke',
                'notes' => 'Standby di lounge LC, siap ditugaskan.',
                'total_hours_served' => 88.5,
            ],
            [
                'stage_name' => 'Clarissa',
                'real_name' => 'Clarissa Aurelia',
                'phone' => '081211223307',
                'price_per_hour' => 220000,
                'commission_rate' => 70.00,
                'status' => 'standby',
                'current_room_id' => null,
                'gender' => 'female',
                'rating' => 4.98,
                'skills' => 'Broad Repertoire, High Tone, Luxury Lounge Attire',
                'notes' => 'Standby di lounge LC, siap ditugaskan.',
                'total_hours_served' => 142.0,
            ],
            [
                'stage_name' => 'Angel',
                'real_name' => 'Angelica Dewi',
                'phone' => '081211223308',
                'price_per_hour' => 150000,
                'commission_rate' => 70.00,
                'status' => 'off',
                'current_room_id' => null,
                'gender' => 'female',
                'rating' => 4.75,
                'skills' => 'Acoustic, Nostalgia 90s, Friendly',
                'notes' => 'Sedang libur / off duty hari ini.',
                'total_hours_served' => 45.0,
            ],
        ];

        $createdCompanions = [];
        foreach ($companionsData as $data) {
            $createdCompanions[$data['stage_name']] = Companion::create($data);
        }

        // 4. Products (F&B & Alcohol/Miras)
        $productsData = [
            // Alcohol / Miras
            [
                'name' => 'Chivas Regal 12 YO (750ml)',
                'category' => 'alcohol',
                'unit' => 'bottle',
                'purchase_price' => 650000,
                'selling_price' => 1450000,
                'stock' => 14,
                'min_stock' => 5,
                'alcohol_content' => '40% ABV',
                'description' => 'Blended Scotch Whisky kelas dunia, kaya rasa madu, hazelnut, dan buah matang.',
            ],
            [
                'name' => 'Johnnie Walker Black Label (750ml)',
                'category' => 'alcohol',
                'unit' => 'bottle',
                'purchase_price' => 720000,
                'selling_price' => 1600000,
                'stock' => 8,
                'min_stock' => 5,
                'alcohol_content' => '40% ABV',
                'description' => 'Iconic blended whisky dengan karakter smokey yang lembut dan mendalam.',
            ],
            [
                'name' => 'Macallan 12 YO Double Cask (700ml)',
                'category' => 'alcohol',
                'unit' => 'bottle',
                'purchase_price' => 1850000,
                'selling_price' => 3950000,
                'stock' => 3, // LOW STOCK ALERT!
                'min_stock' => 5,
                'alcohol_content' => '40% ABV',
                'description' => 'Single malt premium dari Speyside Skotlandia dengan sentuhan oak Amerika dan sherry oak Spanyol.',
            ],
            [
                'name' => 'Jagermeister Herbal Liqueur (700ml)',
                'category' => 'alcohol',
                'unit' => 'bottle',
                'purchase_price' => 520000,
                'selling_price' => 1250000,
                'stock' => 12,
                'min_stock' => 6,
                'alcohol_content' => '35% ABV',
                'description' => 'Liqueur legendaris dari 56 rempah pilihan, disajikan ice-cold shot.',
            ],
            [
                'name' => 'Soju Chum Churum Original (360ml)',
                'category' => 'alcohol',
                'unit' => 'bottle',
                'purchase_price' => 650000 / 10, // 65000
                'selling_price' => 140000,
                'stock' => 28,
                'min_stock' => 10,
                'alcohol_content' => '16.5% ABV',
                'description' => 'Soju Korea otentik dibuat dengan air mineral murni dari pegunungan Gangwon.',
            ],
            [
                'name' => 'Heineken Beer Bucket (5 Cans)',
                'category' => 'alcohol',
                'unit' => 'bucket',
                'purchase_price' => 145000,
                'selling_price' => 320000,
                'stock' => 18,
                'min_stock' => 8,
                'alcohol_content' => '5.0% ABV',
                'description' => 'Bucket 5 kaleng Heineken Premium Lager 330ml dengan es batu kristal segar.',
            ],
            [
                'name' => 'Corona Extra Beer (330ml)',
                'category' => 'alcohol',
                'unit' => 'bottle',
                'purchase_price' => 45000,
                'selling_price' => 95000,
                'stock' => 4, // LOW STOCK ALERT!
                'min_stock' => 8,
                'alcohol_content' => '4.5% ABV',
                'description' => 'Bir impor Meksiko disajikan dingin dengan irisan jeruk nipis segar.',
            ],
            [
                'name' => 'Bintang Crystal Beer Tower (3 Liter)',
                'category' => 'alcohol',
                'unit' => 'tower',
                'purchase_price' => 180000,
                'selling_price' => 395000,
                'stock' => 15,
                'min_stock' => 5,
                'alcohol_content' => '4.3% ABV',
                'description' => 'Tower bir 3 liter super segar disajikan dengan chiller es di tengah.',
            ],
            [
                'name' => 'Cocktail: Blue Kamikaze Shot (6 Glasses)',
                'category' => 'alcohol',
                'unit' => 'portion',
                'purchase_price' => 75000,
                'selling_price' => 210000,
                'stock' => 30,
                'min_stock' => 10,
                'alcohol_content' => '25% ABV',
                'description' => 'Campuran vodka premium, blue curacao, dan freshly squeezed lime juice.',
            ],

            // Softdrinks
            [
                'name' => 'Equil Natural Mineral Water (380ml)',
                'category' => 'softdrink',
                'unit' => 'bottle',
                'purchase_price' => 12000,
                'selling_price' => 35000,
                'stock' => 45,
                'min_stock' => 15,
                'description' => 'Air mineral premium botol kaca dingin.',
            ],
            [
                'name' => 'Coca Cola (Can 330ml)',
                'category' => 'softdrink',
                'unit' => 'can',
                'purchase_price' => 7500,
                'selling_price' => 25000,
                'stock' => 60,
                'min_stock' => 20,
                'description' => 'Minuman bersoda dingin menyegarkan.',
            ],
            [
                'name' => 'Sprite (Can 330ml)',
                'category' => 'softdrink',
                'unit' => 'can',
                'purchase_price' => 7500,
                'selling_price' => 25000,
                'stock' => 60,
                'min_stock' => 20,
                'description' => 'Minuman bersoda dingin menyegarkan.',
            ],
            [
                'name' => 'Fanta (Can 330ml)',
                'category' => 'softdrink',
                'unit' => 'can',
                'purchase_price' => 7500,
                'selling_price' => 25000,
                'stock' => 60,
                'min_stock' => 20,
                'description' => 'Minuman bersoda dingin menyegarkan.',
            ],
            [
                'name' => 'Red Bull Gold Energy Drink (250ml)',
                'category' => 'softdrink',
                'unit' => 'can',
                'purchase_price' => 15000,
                'selling_price' => 45000,
                'stock' => 2, // LOW STOCK ALERT!
                'min_stock' => 10,
                'description' => 'Minuman suplemen berenergi, cocok untuk stamina bernyanyi.',
            ],
            [
                'name' => 'Pitcher Lemon Iced Tea (1.5 Liter)',
                'category' => 'softdrink',
                'unit' => 'tower',
                'purchase_price' => 18000,
                'selling_price' => 75000,
                'stock' => 50,
                'min_stock' => 10,
                'description' => 'Es teh lemon segar dalam pitcher ukuran besar.',
            ],

            // Food / Snacks
            [
                'name' => 'Crispy Chicken Wings BBQ (8 pcs)',
                'category' => 'food',
                'unit' => 'portion',
                'purchase_price' => 28000,
                'selling_price' => 68000,
                'stock' => 35,
                'min_stock' => 10,
                'description' => 'Sayap ayam renyah berlumur saus barbecue gurih dan smoky.',
            ],
            [
                'name' => 'Grand Supreme Pizza (8 Slices)',
                'category' => 'food',
                'unit' => 'portion',
                'purchase_price' => 48000,
                'selling_price' => 115000,
                'stock' => 20,
                'min_stock' => 5,
                'description' => 'Pizza adonan tipis dengan keju mozzarella melimpah, smoked beef, dan paprika.',
            ],
            [
                'name' => 'Nasi Goreng Wagyu Spesial',
                'category' => 'food',
                'unit' => 'portion',
                'purchase_price' => 32000,
                'selling_price' => 78000,
                'stock' => 25,
                'min_stock' => 8,
                'description' => 'Nasi goreng aromatik dengan potongan daging wagyu lembut dan telur mata sapi.',
            ],
            [
                'name' => 'Platter Snack Fiesta',
                'category' => 'food',
                'unit' => 'portion',
                'purchase_price' => 35000,
                'selling_price' => 88000,
                'stock' => 40,
                'min_stock' => 10,
                'description' => 'Kombinasi kentang goreng, sosis bratwurst bakar, calamari ring, dan onion rings.',
            ],
            [
                'name' => 'Fresh Fruit Platter Seasonal',
                'category' => 'food',
                'unit' => 'portion',
                'purchase_price' => 25000,
                'selling_price' => 65000,
                'stock' => 15,
                'min_stock' => 5,
                'description' => 'Irisan semangka merah, melon manis, nanas, dan anggur segar dingin.',
            ],
            [
                'name' => 'French Fries',
                'category' => 'food',
                'unit' => 'portion',
                'purchase_price' => 20000,
                'selling_price' => 35000,
                'stock' => 15,
                'min_stock' => 5,
                'description' => 'Kentang Goreng yang Gurih dan renyah dibumbui dengan cita rasa asin yang pas untuk menemani waktu bernyanyi.',
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $data) {
            $p = Product::create($data);
            $createdProducts[$p->name] = $p;
        }

        // 5. Packages (Bundling Packages: Room + LC + Alcohol/F&B)
        $pkg1 = Package::create([
            'name' => 'Paket Chill & Sing (Small Room)',
            'room_type' => 'small',
            'room_hours' => 2,
            'include_lc_qty' => 0,
            'include_lc_hours' => 0,
            'price' => 350000,
            'description' => 'Sewa kamar Small 2 Jam + 1 Heineken Beer Bucket + 1 French Fries Platter. Hemat hingga 25%!',
        ]);
        PackageItem::create([
            'package_id' => $pkg1->id,
            'product_id' => $createdProducts['Heineken Beer Bucket (5 Cans)']->id,
            'quantity' => 1,
        ]);
        PackageItem::create([
            'package_id' => $pkg1->id,
            'product_id' => $createdProducts['Platter Snack Fiesta']->id,
            'quantity' => 1,
        ]);

        $pkg2 = Package::create([
            'name' => 'Paket Party Vibes (Medium Room)',
            'room_type' => 'medium',
            'room_hours' => 3,
            'include_lc_qty' => 1,
            'include_lc_hours' => 2,
            'price' => 1850000,
            'description' => 'Sewa kamar Medium 3 Jam + 1 Botol Jagermeister 700ml + 2 Red Bull + 1 Pemandu Lagu (LC) 2 Jam + Platter Snack. Rekomendasi terfavorit!',
        ]);
        PackageItem::create([
            'package_id' => $pkg2->id,
            'product_id' => $createdProducts['Jagermeister Herbal Liqueur (700ml)']->id,
            'quantity' => 1,
        ]);
        PackageItem::create([
            'package_id' => $pkg2->id,
            'product_id' => $createdProducts['Red Bull Gold Energy Drink (250ml)']->id,
            'quantity' => 2,
        ]);
        PackageItem::create([
            'package_id' => $pkg2->id,
            'product_id' => $createdProducts['Platter Snack Fiesta']->id,
            'quantity' => 1,
        ]);

        $pkg3 = Package::create([
            'name' => 'Royal Imperial Bundling (VIP Suite)',
            'room_type' => 'vip',
            'room_hours' => 4,
            'include_lc_qty' => 2,
            'include_lc_hours' => 3,
            'price' => 4600000,
            'description' => 'Suite VIP 4 Jam + 1 Botol Chivas Regal 12 YO + 2 Pemandu Lagu (LC) 3 Jam + 1 Pizza Grand Supreme + 1 Fresh Fruit Platter. Pengalaman pesta privat kelas atas!',
        ]);
        PackageItem::create([
            'package_id' => $pkg3->id,
            'product_id' => $createdProducts['Chivas Regal 12 YO (750ml)']->id,
            'quantity' => 1,
        ]);
        PackageItem::create([
            'package_id' => $pkg3->id,
            'product_id' => $createdProducts['Grand Supreme Pizza (8 Slices)']->id,
            'quantity' => 1,
        ]);
        PackageItem::create([
            'package_id' => $pkg3->id,
            'product_id' => $createdProducts['Fresh Fruit Platter Seasonal']->id,
            'quantity' => 1,
        ]);

        // 6. Promos & FAQ
        Promo::create([
            'title' => 'Happy Hour Sing-Along Diskon 50% Room',
            'badge' => 'SPECIAL DEAL',
            'discount_text' => '50% OFF SEWA ROOM',
            'description' => 'Nikmati diskon 50% biaya sewa semua tipe kamar (Small, Medium, Large) setiap hari Senin hingga Kamis mulai jam 13.00 s/d 18.00 WIB.',
            'terms' => '1. Berlaku untuk check-in antara 13:00 - 18:00 WIB. 2. Minimal sewa 2 jam. 3. Tidak dapat digabungkan dengan promo paket bundling.',
        ]);

        Promo::create([
            'title' => 'Bucket Beer Madness Buy 2 Get 1 Free',
            'badge' => 'BEER LOVER',
            'discount_text' => 'BUY 2 BUCKET GET 1 FREE',
            'description' => 'Beli 2 Bucket Heineken atau Bintang Crystal, dapatkan gratis 1 Bucket tambahan dingin untuk menemani sesi karaoke tanpa henti!',
            'terms' => '1. Berlaku setiap hari dari jam 18:00 hingga tutup. 2. Hanya untuk varian bir yang sama.',
        ]);

        Promo::create([
            'title' => 'VIP Member Night & Complimentary Drink',
            'badge' => 'VIP EXCLUSIVE',
            'discount_text' => 'FREE COCKTAIL PITCHER',
            'description' => 'Booking kamar VIP minimal 3 jam, nikmati gratis 1 Pitcher Signature Cocktail racikan bartender kami dan free parking valet.',
            'terms' => '1. Khusus pemesanan kamar VIP-01 atau VIP-02. 2. Reservasi minimal 1 hari sebelumnya.',
        ]);

        // 7. Songs (Song Finder catalog)
        $songsData = [
            ['title' => 'Dan', 'artist' => 'Sheila on 7', 'genre' => 'Pop / Nostalgia', 'language' => 'Indonesia', 'lyrics_snippet' => 'Dan bila esok datang kembali seperti sedia kala...', 'is_favorite' => true, 'views_count' => 1520],
            ['title' => 'Kangen', 'artist' => 'Dewa 19', 'genre' => 'Rock / Pop', 'language' => 'Indonesia', 'lyrics_snippet' => 'Kutumpahkan semua rasa rinduku yang mendalam...', 'is_favorite' => true, 'views_count' => 2100],
            ['title' => 'Hati-Hati di Jalan', 'artist' => 'Tulus', 'genre' => 'Pop', 'language' => 'Indonesia', 'lyrics_snippet' => 'Kukira kita asam dan garam dan kita bertemu di belanga...', 'is_favorite' => true, 'views_count' => 1840],
            ['title' => 'Rungkad', 'artist' => 'Happy Asmara ft. Denny Caknan', 'genre' => 'Dangdut Koplo', 'language' => 'Indonesia', 'lyrics_snippet' => 'Rungkad entek-entekan kelangan koe seng paling tak sayang...', 'is_favorite' => true, 'views_count' => 3400],
            ['title' => 'Nemen', 'artist' => 'Gildcoustic', 'genre' => 'Dangdut Koplo', 'language' => 'Indonesia', 'lyrics_snippet' => 'Kudune kowe ngerti tresnoku nomer siji...', 'is_favorite' => false, 'views_count' => 920],
            ['title' => 'Bohemian Rhapsody', 'artist' => 'Queen', 'genre' => 'Classic Rock', 'language' => 'English', 'lyrics_snippet' => 'Is this the real life? Is this just fantasy? Caught in a landslide...', 'is_favorite' => true, 'views_count' => 2450],
            ['title' => 'Die With A Smile', 'artist' => 'Lady Gaga & Bruno Mars', 'genre' => 'Pop', 'language' => 'English', 'lyrics_snippet' => 'If the world was ending I would wanna be next to you...', 'is_favorite' => true, 'views_count' => 2890],
            ['title' => 'Rolling in the Deep', 'artist' => 'Adele', 'genre' => 'Pop / Soul', 'language' => 'English', 'lyrics_snippet' => 'There is a fire starting in my heart, reaching a fever pitch...', 'is_favorite' => false, 'views_count' => 1100],
            ['title' => 'Yue Liang Dai Biao Wo De Xin (月亮代表我的心)', 'artist' => 'Teresa Teng', 'genre' => 'Classic Mandarin', 'language' => 'Mandarin', 'lyrics_snippet' => 'Ni wen wo ai ni you duo shen, wo ai ni you ji fen...', 'is_favorite' => true, 'views_count' => 1760],
            ['title' => 'Tong Hua (童话)', 'artist' => 'Michael Wong (Guang Liang)', 'genre' => 'Mandarin Pop', 'language' => 'Mandarin', 'lyrics_snippet' => 'Wo yuan bian cheng tong hua li ni ai de na ge tian shi...', 'is_favorite' => false, 'views_count' => 890],
            ['title' => 'Dynamite', 'artist' => 'BTS', 'genre' => 'K-Pop', 'language' => 'Korea', 'lyrics_snippet' => 'Cause I, I, I am in the stars tonight, so watch me bring the fire...', 'is_favorite' => true, 'views_count' => 1430],
            ['title' => 'Pink Venom', 'artist' => 'BLACKPINK', 'genre' => 'K-Pop', 'language' => 'Korea', 'lyrics_snippet' => 'Kick in the door, waving the coco, papara papara...', 'is_favorite' => false, 'views_count' => 980],
            ['title' => 'Hotel California', 'artist' => 'Eagles', 'genre' => 'Classic Rock', 'language' => 'English', 'lyrics_snippet' => 'On a dark desert highway, cool wind in my hair...', 'is_favorite' => false, 'views_count' => 1650],
            ['title' => 'Komang', 'artist' => 'Raim Laode', 'genre' => 'Pop / Indie', 'language' => 'Indonesia', 'lyrics_snippet' => 'Sebab kau terlalu indah dari sekedar kata...', 'is_favorite' => true, 'views_count' => 1290],
        ];

        foreach ($songsData as $song) {
            Song::create($song);
        }

        // 8. Active Cashier Shift
        $shift = CashierShift::create([
            'user_id' => $kasir1->id,
            'shift_name' => 'Shift Siang Kasir 1',
            'start_time' => Carbon::now()->setTime(11, 0, 0),
            'starting_cash' => 1000000,
            'total_sales_cash' => 2450000,
            'total_sales_noncash' => 5200000,
            'status' => 'open',
            'notes' => 'Kasir buka modal awal Rp 1.000.000 (pecahan 50rb & 100rb)',
        ]);

        // 9. Sample Current Active Bookings & Completed Sales
        // Booking 1: Online Pending (untuk demonstrasi kasir konfirmasi reservasi)
        $bookingOnline = Booking::create([
            'booking_code' => 'BK-' . date('Ymd') . '-001',
            'customer_name' => 'Hendra Kusuma',
            'customer_phone' => '081288991122',
            'user_id' => $customer->id,
            'room_id' => $createdRooms['M-02']->id,
            'package_id' => null,
            'start_time' => Carbon::now()->addHours(2),
            'end_time' => Carbon::now()->addHours(5),
            'duration_hours' => 3,
            'booking_type' => 'online',
            'status' => 'pending',
            'include_lc' => true,
            'lc_qty' => 1,
            'lc_hours' => 2,
            'room_charge' => 140000 * 3,
            'lc_charge' => 175000 * 2,
            'preorder_total' => 320000 + 88000,
            'total_estimated_price' => (140000 * 3) + (175000 * 2) + 320000 + 88000,
            'whatsapp_sent' => true,
            'notes' => 'Mohon siapkan mic tambahan dan es batu ekstra.',
        ]);
        BookingItem::create([
            'booking_id' => $bookingOnline->id,
            'product_id' => $createdProducts['Heineken Beer Bucket (5 Cans)']->id,
            'quantity' => 1,
            'price' => 320000,
            'subtotal' => 320000,
        ]);
        BookingItem::create([
            'booking_id' => $bookingOnline->id,
            'product_id' => $createdProducts['Platter Snack Fiesta']->id,
            'quantity' => 1,
            'price' => 88000,
            'subtotal' => 88000,
        ]);

        // Booking 2: Occupied Room S-02 (Walk-in)
        $bookingOcc1 = Booking::create([
            'booking_code' => 'BK-' . date('Ymd') . '-002',
            'customer_name' => 'Pak Joko & Rekan',
            'customer_phone' => '081344556677',
            'user_id' => null,
            'room_id' => $createdRooms['S-02']->id,
            'package_id' => null,
            'start_time' => Carbon::now()->subMinutes(45),
            'end_time' => Carbon::now()->subMinutes(45)->addHours(2),
            'duration_hours' => 2,
            'booking_type' => 'walk_in',
            'status' => 'confirmed',
            'include_lc' => true,
            'lc_qty' => 1,
            'lc_hours' => 2,
            'room_charge' => 85000 * 2,
            'lc_charge' => 175000 * 2,
            'preorder_total' => 140000,
            'total_estimated_price' => (85000 * 2) + (175000 * 2) + 140000,
            'whatsapp_sent' => false,
        ]);
        BookingCompanion::create([
            'booking_id' => $bookingOcc1->id,
            'companion_id' => $createdCompanions['Luna']->id,
            'hours' => 2,
            'price_per_hour' => 175000,
            'subtotal' => 350000,
            'status' => 'active',
        ]);
        BookingItem::create([
            'booking_id' => $bookingOcc1->id,
            'product_id' => $createdProducts['Soju Chum Churum Original (360ml)']->id,
            'quantity' => 1,
            'price' => 140000,
            'subtotal' => 140000,
        ]);

        // Booking 3: Occupied Room VIP-01 (Paket Bundling VIP)
        $bookingOcc2 = Booking::create([
            'booking_code' => 'BK-' . date('Ymd') . '-003',
            'customer_name' => 'Ko Kevin (VIP Member)',
            'customer_phone' => '081899001122',
            'user_id' => null,
            'room_id' => $createdRooms['VIP-01']->id,
            'package_id' => $pkg3->id,
            'start_time' => Carbon::now()->subHours(1),
            'end_time' => Carbon::now()->subHours(1)->addHours(4),
            'duration_hours' => 4,
            'booking_type' => 'walk_in',
            'status' => 'confirmed',
            'include_lc' => true,
            'lc_qty' => 2,
            'lc_hours' => 3,
            'room_charge' => 1800000,
            'lc_charge' => 1200000,
            'preorder_total' => 1600000,
            'total_estimated_price' => 4600000,
            'whatsapp_sent' => false,
            'notes' => 'Tamu VIP penting, sajikan Chivas on the rocks.',
        ]);
        BookingCompanion::create([
            'booking_id' => $bookingOcc2->id,
            'companion_id' => $createdCompanions['Sherly']->id,
            'hours' => 3,
            'price_per_hour' => 200000,
            'subtotal' => 600000,
            'status' => 'active',
        ]);
        BookingCompanion::create([
            'booking_id' => $bookingOcc2->id,
            'companion_id' => $createdCompanions['Jessica']->id,
            'hours' => 3,
            'price_per_hour' => 200000,
            'subtotal' => 600000,
            'status' => 'active',
        ]);

        // 10. Sample Completed Sales (for rich analytics & thermal receipt preview!)
        // Sale 1: Cash payment with alcohol & LC
        $sale1 = Sale::create([
            'invoice_number' => 'INV-' . date('Ymd') . '-0001',
            'booking_id' => null,
            'room_id' => $createdRooms['M-01']->id,
            'user_id' => $kasir1->id,
            'cashier_shift_id' => $shift->id,
            'customer_name' => 'Ferry Wijaya',
            'customer_phone' => '081233445566',
            'total_room' => 280000, // 2 jam Medium
            'total_fnb' => 1450000 + 88000, // Chivas + Snack Platter
            'total_service' => 350000, // LC Bella 2 Jam
            'discount' => 50000,
            'grand_total' => 280000 + 1450000 + 88000 + 350000 - 50000, // 2.118.000
            'paid_amount' => 2200000,
            'change_amount' => 82000,
            'payment_method' => 'cash',
            'is_complimentary' => false,
            'status' => 'paid',
            'notes' => 'Regular customer, discount voucher membership',
        ]);
        SaleItem::create([
            'sale_id' => $sale1->id,
            'item_type' => 'room',
            'item_id' => $createdRooms['M-01']->id,
            'item_name' => 'Sewa Room M-01 (Medium) x 2 Jam',
            'quantity' => 2,
            'purchase_price' => 0,
            'price' => 140000,
            'subtotal' => 280000,
        ]);
        SaleItem::create([
            'sale_id' => $sale1->id,
            'item_type' => 'fnb',
            'item_id' => $createdProducts['Chivas Regal 12 YO (750ml)']->id,
            'item_name' => 'Chivas Regal 12 YO (750ml)',
            'quantity' => 1,
            'purchase_price' => 650000,
            'price' => 1450000,
            'subtotal' => 1450000,
        ]);
        SaleItem::create([
            'sale_id' => $sale1->id,
            'item_type' => 'fnb',
            'item_id' => $createdProducts['Platter Snack Fiesta']->id,
            'item_name' => 'Platter Snack Fiesta',
            'quantity' => 1,
            'purchase_price' => 35000,
            'price' => 88000,
            'subtotal' => 88000,
        ]);
        SaleItem::create([
            'sale_id' => $sale1->id,
            'item_type' => 'service',
            'item_id' => $createdCompanions['Bella']->id,
            'item_name' => 'Layanan Pemandu Lagu: Bella (2 Jam)',
            'quantity' => 2,
            'purchase_price' => 122500, // LC cut
            'price' => 175000,
            'subtotal' => 350000,
        ]);

        // Sale 2: QRIS payment with beer & wings
        $sale2 = Sale::create([
            'invoice_number' => 'INV-' . date('Ymd') . '-0002',
            'booking_id' => null,
            'room_id' => $createdRooms['S-01']->id,
            'user_id' => $kasir1->id,
            'cashier_shift_id' => $shift->id,
            'customer_name' => 'Rudy Pratama',
            'customer_phone' => '081766554433',
            'total_room' => 170000, // 2 jam Small
            'total_fnb' => 320000 + 68000, // Heineken Bucket + Wings
            'total_service' => 0,
            'discount' => 0,
            'grand_total' => 170000 + 320000 + 68000, // 558.000
            'paid_amount' => 558000,
            'change_amount' => 0,
            'payment_method' => 'qris',
            'is_complimentary' => false,
            'status' => 'paid',
        ]);
        SaleItem::create([
            'sale_id' => $sale2->id,
            'item_type' => 'room',
            'item_id' => $createdRooms['S-01']->id,
            'item_name' => 'Sewa Room S-01 (Small) x 2 Jam',
            'quantity' => 2,
            'purchase_price' => 0,
            'price' => 85000,
            'subtotal' => 170000,
        ]);
        SaleItem::create([
            'sale_id' => $sale2->id,
            'item_type' => 'fnb',
            'item_id' => $createdProducts['Heineken Beer Bucket (5 Cans)']->id,
            'item_name' => 'Heineken Beer Bucket (5 Cans)',
            'quantity' => 1,
            'purchase_price' => 145000,
            'price' => 320000,
            'subtotal' => 320000,
        ]);
        SaleItem::create([
            'sale_id' => $sale2->id,
            'item_type' => 'fnb',
            'item_id' => $createdProducts['Crispy Chicken Wings BBQ (8 pcs)']->id,
            'item_name' => 'Crispy Chicken Wings BBQ (8 pcs)',
            'quantity' => 1,
            'purchase_price' => 28000,
            'price' => 68000,
            'subtotal' => 68000,
        ]);

        // Sale 3: Complimentary Booking (Owner Privilege 100% discount demonstration)
        $sale3 = Sale::create([
            'invoice_number' => 'INV-' . date('Ymd') . '-0003',
            'booking_id' => null,
            'room_id' => $createdRooms['L-01']->id,
            'user_id' => $kasir1->id,
            'cashier_shift_id' => $shift->id,
            'customer_name' => 'Relasi Owner (Keluarga Pak Hartono)',
            'customer_phone' => '081122334455',
            'total_room' => 480000,
            'total_fnb' => 250000,
            'total_service' => 0,
            'discount' => 730000, // 100% discount
            'grand_total' => 0,
            'paid_amount' => 0,
            'change_amount' => 0,
            'payment_method' => 'complimentary',
            'is_complimentary' => true,
            'complimentary_reason' => 'Otorisasi Owner: Kunjungan relasi bisnis dan keluarga Pak Hartono',
            'status' => 'paid',
            'notes' => 'Complimentary 100% sesuai instruksi Owner, tidak memengaruhi selisih laci kasir.',
        ]);
        SaleItem::create([
            'sale_id' => $sale3->id,
            'item_type' => 'room',
            'item_id' => $createdRooms['L-01']->id,
            'item_name' => 'Sewa Room L-01 (Large) x 2 Jam (Complimentary)',
            'quantity' => 2,
            'purchase_price' => 0,
            'price' => 240000,
            'subtotal' => 480000,
        ]);
        SaleItem::create([
            'sale_id' => $sale3->id,
            'item_type' => 'fnb',
            'item_id' => $createdProducts['Grand Supreme Pizza (8 Slices)']->id,
            'item_name' => 'Grand Supreme Pizza (8 Slices)',
            'quantity' => 1,
            'purchase_price' => 48000,
            'price' => 115000,
            'subtotal' => 115000,
        ]);
        SaleItem::create([
            'sale_id' => $sale3->id,
            'item_type' => 'fnb',
            'item_id' => $createdProducts['Fresh Fruit Platter Seasonal']->id,
            'item_name' => 'Fresh Fruit Platter Seasonal',
            'quantity' => 1,
            'purchase_price' => 25000,
            'price' => 65000,
            'subtotal' => 65000,
        ]);
        SaleItem::create([
            'sale_id' => $sale3->id,
            'item_type' => 'fnb',
            'item_id' => $createdProducts['Pitcher Lemon Iced Tea (1.5 Liter)']->id,
            'item_name' => 'Pitcher Lemon Iced Tea (1.5 Liter)',
            'quantity' => 1,
            'purchase_price' => 18000,
            'price' => 70000,
            'subtotal' => 70000,
        ]);
    }
}
