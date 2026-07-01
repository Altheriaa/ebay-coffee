<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogObserver
{
    /**
     * Handle the "created" event.
     */
    public function created(Model $model): void
    {
        $this->logActivity('created', $model, null, $model->getAttributes());
    }

    /**
     * Handle the "updated" event.
     */
    public function updated(Model $model): void
    {
        $oldValues = [];
        $newValues = [];

        foreach ($model->getDirty() as $key => $newValue) {
            $oldValues[$key] = $model->getOriginal($key);
            $newValues[$key] = $newValue;
        }

        // Jangan log jika tidak ada perubahan berarti
        $skip = ['updated_at', 'remember_token'];
        $filtered = array_filter($newValues, fn($k) => !in_array($k, $skip), ARRAY_FILTER_USE_KEY);
        if (empty($filtered)) {
            return;
        }

        $this->logActivity('updated', $model, $oldValues, $newValues);
    }

    /**
     * Handle the "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->logActivity('deleted', $model, $model->getAttributes(), null);
    }

    /**
     * Catat aktivitas ke database.
     */
    protected function logActivity(string $action, Model $model, ?array $oldValues, ?array $newValues): void
    {
        $user = Auth::user();

        // Hanya log aktivitas yang dilakukan oleh admin
        if (!$user || $user->role !== 'admin') {
            return;
        }

        $modelName = class_basename($model);
        $description = $this->buildDescription($action, $model, $user);

        // Bersihkan data sensitif sebelum disimpan
        $sensitiveKeys = ['password', 'remember_token'];
        if ($oldValues) {
            $oldValues = array_diff_key($oldValues, array_flip($sensitiveKeys));
        }
        if ($newValues) {
            $newValues = array_diff_key($newValues, array_flip($sensitiveKeys));
        }

        ActivityLog::create([
            'user_id'    => $user->id,
            'action'     => $action,
            'model_type' => $modelName,
            'model_id'   => $model->id ?? 0,
            'description'=> $description,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => request()?->ip(),
        ]);
    }

    /**
     * Buat deskripsi human-readable per model.
     */
    protected function buildDescription(string $action, Model $model, $user): string
    {
        $modelName  = class_basename($model);
        $adminName  = $user->name;
        $recordName = $this->getRecordName($model, $modelName);

        $labelMap = [
            'Product'  => 'Produk',
            'Category' => 'Kategori',
            'Customer' => 'Pelanggan',
            'Order'    => 'Pesanan',
            'User'     => 'User',
        ];
        $label = $labelMap[$modelName] ?? $modelName;

        $actionLabel = match ($action) {
            'created' => 'menambahkan',
            'updated' => 'mengubah',
            'deleted' => 'menghapus',
            default   => $action,
        };

        $base = "Admin '{$adminName}' {$actionLabel} {$label} '{$recordName}'";

        if ($action === 'updated') {
            $base .= $this->buildUpdateDetail($model, $modelName);
        }

        return $base;
    }

    /**
     * Ambil nama record yang mudah dibaca.
     */
    protected function getRecordName(Model $model, string $modelName): string
    {
        return match ($modelName) {
            'Product'  => $model->nama_product ?? "#{$model->id}",
            'Category' => $model->nama_kategori ?? "#{$model->id}",
            'Customer' => $model->nama ?? "#{$model->id}",
            'Order'    => $model->invoice_number ?? "#{$model->id}",
            'User'     => $model->name ?? "#{$model->id}",
            default    => $model->name ?? $model->nama ?? "#{$model->id}",
        };
    }

    /**
     * Buat detail perubahan untuk aksi update.
     */
    protected function buildUpdateDetail(Model $model, string $modelName): string
    {
        $changes = [];

        foreach ($model->getDirty() as $key => $newValue) {
            $oldValue = $model->getOriginal($key);

            // Skip kolom teknis
            if (in_array($key, ['updated_at', 'created_at', 'remember_token', 'password'])) {
                continue;
            }

            $changes[] = match ($key) {
                'stok'         => "stok: {$oldValue} → {$newValue}",
                'harga'        => "harga: Rp " . number_format((float)$oldValue, 0, ',', '.') . " → Rp " . number_format((float)$newValue, 0, ',', '.'),
                'status'       => "status pesanan: {$oldValue} → {$newValue}",
                'status_payment' => "status bayar: {$oldValue} → {$newValue}",
                'nama_kategori'=> "nama kategori: {$oldValue} → {$newValue}",
                'role'         => "role: {$oldValue} → {$newValue}",
                default        => "{$key}: {$oldValue} → {$newValue}",
            };
        }

        return $changes ? ' (' . implode(', ', $changes) . ')' : '';
    }
}
