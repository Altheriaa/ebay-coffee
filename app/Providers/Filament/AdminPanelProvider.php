<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('bay-coffee-admin')
            ->path('baycoffee-admin')
            ->brandName('Bay Coffee Admin')
            ->login()
            ->colors([
                // Warm brown from website's --color-surface-tint (#745853)
                'primary' => [
                    50  => '250, 241, 239',   // #faf1ef
                    100 => '243, 224, 219',   // #f3e0db
                    200 => '231, 196, 187',   // #e7c4bb
                    300 => '214, 161, 148',   // #d6a194
                    400 => '192, 125, 110',   // #c07d6e
                    500 => '163, 94, 79',     // #a35e4f
                    600 => '116, 88, 83',     // #745853
                    700 => '95, 70, 66',      // #5f4642
                    800 => '72, 52, 48',      // #483430
                    900 => '62, 39, 35',      // #3e2723
                    950 => '39, 19, 16',      // #271310
                ],
                // Warm caramel from website's --color-secondary (#7d562d)
                'warning' => [
                    50  => '255, 247, 237',   // #fff7ed
                    100 => '255, 236, 210',   // #ffecd2
                    200 => '255, 220, 189',   // #ffdcbd
                    300 => '240, 189, 139',   // #f0bd8b
                    400 => '225, 163, 100',   // #e1a364
                    500 => '195, 130, 56',    // #c38238
                    600 => '125, 86, 45',     // #7d562d
                    700 => '98, 63, 24',      // #623f18
                    800 => '74, 46, 14',      // #4a2e0e
                    900 => '44, 22, 0',       // #2c1600
                    950 => '28, 14, 0',       // #1c0e00
                ],
                'danger'  => Color::Rose,
                'success' => Color::Emerald,
                'info'    => [
                    50  => '247, 244, 242',   // #f7f4f2 warm gray
                    100 => '238, 233, 229',   // #eee9e5
                    200 => '223, 215, 210',   // #dfd7d2
                    300 => '200, 188, 182',   // #c8bcb6
                    400 => '168, 152, 145',   // #a89891
                    500 => '130, 116, 114',   // #827472
                    600 => '107, 94, 91',     // #6b5e5b
                    700 => '86, 75, 72',      // #564b48
                    800 => '65, 56, 53',      // #413835
                    900 => '50, 68, 66',      // #504442
                    950 => '38, 24, 21',      // #261815
                ],
                // Warm-tinted gray to match the coffee aesthetic
                'gray' => [
                    50  => '255, 248, 246',   // #fff8f6 - page background
                    100 => '248, 237, 234',   // #f8edea - card/input backgrounds
                    200 => '235, 220, 215',   // #ebdcd7 - borders, dividers
                    300 => '210, 195, 190',   // #d2c3be - muted borders
                    400 => '165, 145, 140',   // #a5918c - placeholder text, muted icons
                    500 => '130, 112, 108',   // #82706c - secondary text, sidebar icons
                    600 => '100, 85, 80',     // #645550 - body text
                    700 => '75, 62, 58',      // #4b3e3a - strong text, headings
                    800 => '50, 40, 36',      // #322824 - dark text
                    900 => '35, 25, 22',      // #231916 - near-black
                    950 => '22, 14, 12',      // #160e0c - darkest
                ],
            ])
            ->font('Plus Jakarta Sans')
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
