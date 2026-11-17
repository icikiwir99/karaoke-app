<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'customer_name',
        'customer_phone',
        'user_id',
        'room_id',
        'package_id',
        'start_time',
        'end_time',
        'duration_hours',
        'booking_type',
        'status',
        'include_lc',
        'lc_qty',
        'lc_hours',
        'room_charge',
        'lc_charge',
        'preorder_total',
        'total_estimated_price',
        'whatsapp_sent',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_hours' => 'integer',
        'include_lc' => 'boolean',
        'lc_qty' => 'integer',
        'lc_hours' => 'integer',
        'room_charge' => 'decimal:2',
        'lc_charge' => 'decimal:2',
        'preorder_total' => 'decimal:2',
        'total_estimated_price' => 'decimal:2',
        'whatsapp_sent' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function companions()
    {
        return $this->hasMany(BookingCompanion::class);
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function sale()
    {
        return $this->hasOne(Sale::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_estimated_price, 0, ',', '.');
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        $labels = [
            'cash' => 'Bayar di Kasir (Cash on Arrival)',
            'qris' => 'QRIS (Instant QR Payment)',
            'bca' => 'Transfer Bank BCA (Virtual Account)',
            'mandiri' => 'Transfer Bank Mandiri (VA)',
            'bni' => 'Transfer Bank BNI (VA)',
            'bri' => 'Transfer Bank BRI (VA)',
            'dana' => 'E-Wallet DANA',
            'ovo' => 'E-Wallet OVO',
            'gopay' => 'E-Wallet GoPay',
        ];

        return $labels[$this->payment_method] ?? ucfirst($this->payment_method ?? 'Bayar di Kasir');
    }

    public function generateWhatsAppUrl(string $adminPhone = '6281274241875'): string
    {
        $text = "Halo Admin Grand Karaoke & Lounge,\n";
        $text .= "Saya ingin konfirmasi pemesanan / reservasi karaoke:\n\n";
        $text .= "📌 *Kode Booking:* " . $this->booking_code . "\n";
        $text .= "👤 *Nama Pemesan:* " . $this->customer_name . "\n";
        $text .= "📱 *No. WhatsApp:* " . $this->customer_phone . "\n";
        $text .= "🚪 *Kamar / Paket:* " . ($this->room ? $this->room->room_number . ' (' . ucfirst($this->room->type) . ')' : '-');
        if ($this->package) {
            $text .= " + " . $this->package->name;
        }
        $text .= "\n";
        $text .= "📅 *Tanggal & Jam:* " . $this->start_time->format('d M Y, H:i') . " WIB\n";
        $text .= "⏳ *Durasi:* " . $this->duration_hours . " Jam" . ($this->package && str_contains(strtolower($this->package->name), 'best deal') ? " (Free Room Sepuasnya 17.00 - 03.30 WIB)" : "") . "\n";

        if ($this->include_lc) {
            $lcNames = $this->companions->map(function($c) {
                return ($c->companion ? $c->companion->stage_name : 'LC') . " (Rp " . number_format($c->price_per_hour, 0, ',', '.') . "/jam)";
            })->join(', ');

            $text .= "💃 *Rincian LC:* " . ($lcNames ? $lcNames . " x " . $this->lc_hours . " Jam" : ($this->lc_qty . " Orang x " . $this->lc_hours . " Jam")) . " - Total: Rp " . number_format($this->lc_charge, 0, ',', '.') . "\n";
        }

        if ($this->items->count() > 0) {
            $text .= "\n🍾 *Rincian Pre-Order F&B / Minuman:*\n";
            foreach ($this->items as $item) {
                $text .= "  • " . ($item->product ? $item->product->name : 'Item') . " (" . $item->quantity . "x) : Rp " . number_format($item->subtotal, 0, ',', '.') . "\n";
            }
        }

        $text .= "\n💳 *Metode Pembayaran:* " . $this->payment_method_label . "\n";
        $text .= "💰 *Total Estimasi Biaya:* " . $this->formatted_total . "\n";

        if (!empty($this->notes)) {
            $text .= "📝 *Catatan:* " . $this->notes . "\n";
        }
        $text .= "\nMohon segera diproses dan dikonfirmasi. Terima kasih!";

        return "https://wa.me/" . preg_replace('/[^0-9]/', '', $adminPhone) . "?text=" . urlencode($text);
    }
}
