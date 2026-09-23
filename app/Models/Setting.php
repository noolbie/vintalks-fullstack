<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Setting: pasangan key-value untuk pengaturan aplikasi (tabel `settings`).
 */
class Setting extends Model
{
    // Tabel memakai `key` sebagai primary key string (bukan auto-increment id).
    protected $table = 'settings';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    // Kolom yang boleh diisi (key dan value).
    protected $fillable = ['key', 'value'];

    // ==== Helper pengaturan ====

    // Ambil nilai pengaturan berdasarkan key, dengan nilai default jika belum ada.
    public static function get(string $key, mixed $default = null): mixed
    {
        $row = static::query()->where('key', $key)->first();

        return $row?->value ?? $default;
    }

    // Simpan/ubah nilai pengaturan sesuai key (menggunakan updateOrCreate).
    public static function set(string $key, mixed $value): void
    {
        $stored = is_null($value)
            ? null
            : (is_scalar($value) ? (string) $value : json_encode($value));

        static::query()->updateOrCreate(['key' => $key], ['value' => $stored]);
    }

    // Nilai metode pembayaran yang aktif, disaring agar hanya yang valid dari enum.
    /**
     * List of enabled payment method values (e.g. ['qris', 'transfer_bank']).
     */
    public static function paymentMethodValues(): array
    {
        $raw = static::get('payment_methods', []);

        $values = is_string($raw) ? json_decode($raw, true) : $raw;
        $values = is_array($values) ? $values : [];

        return array_values(array_filter($values, fn ($v) => PaymentMethod::tryFrom((string) $v) !== null));
    }

    // Pasangan [value => label] metode pembayaran aktif untuk ditampilkan di dropdown.
    /**
     * Enabled payment methods as [value => label] map.
     */
    public static function paymentMethods(): array
    {
        $result = [];

        foreach (static::paymentMethodValues() as $value) {
            $method = PaymentMethod::from($value);
            $result[] = ['value' => $method->value, 'label' => $method->label(), 'is_bank_transfer' => $method->isBankTransfer()];
        }

        return $result;
    }
}