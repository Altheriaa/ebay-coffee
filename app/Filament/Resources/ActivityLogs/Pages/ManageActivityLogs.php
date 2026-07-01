<?php

namespace App\Filament\Resources\ActivityLogs\Pages;

use App\Filament\Resources\ActivityLogs\ActivityLogResource;
use Filament\Resources\Pages\ManageRecords;

class ManageActivityLogs extends ManageRecords
{
    protected static string $resource = ActivityLogResource::class;

    /**
     * Tidak ada tombol "Create" karena log bersifat read-only.
     */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
