<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\ActivityTrendChart;
use App\Filament\Widgets\OpenAlertsWidget;
use App\Filament\Widgets\RetentionOverviewWidget;
use App\Filament\Widgets\RevenueSpendTrendChart;
use App\Filament\Widgets\RoiPayTrendChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('YWXQ Admin')
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('12.5rem')
            ->collapsedSidebarWidth('3.5rem')
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString(<<<'HTML'
                    <style>
                        @media (min-width: 1024px) {
                            .fi-body.fi-body-has-sidebar-collapsible-on-desktop:not(.fi-body-has-top-navigation) .fi-sidebar:not(.fi-sidebar-open) {
                                width: var(--collapsed-sidebar-width) !important;
                                min-width: var(--collapsed-sidebar-width) !important;
                                max-width: var(--collapsed-sidebar-width) !important;
                                overflow-x: hidden;
                            }

                            .fi-body.fi-body-has-sidebar-collapsible-on-desktop:not(.fi-body-has-top-navigation) .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-header {
                                padding-inline: 0.25rem !important;
                            }

                            .fi-body.fi-body-has-sidebar-collapsible-on-desktop:not(.fi-body-has-top-navigation) .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-nav {
                                align-items: center;
                                padding-inline: 0.25rem !important;
                                scrollbar-gutter: auto;
                            }

                            .fi-body.fi-body-has-sidebar-collapsible-on-desktop:not(.fi-body-has-top-navigation) .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-nav-groups {
                                width: 100%;
                                align-items: center;
                                gap: 1rem;
                                margin-inline: 0 !important;
                            }

                            .fi-body.fi-body-has-sidebar-collapsible-on-desktop:not(.fi-body-has-top-navigation) .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-group,
                            .fi-body.fi-body-has-sidebar-collapsible-on-desktop:not(.fi-body-has-top-navigation) .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-group-items,
                            .fi-body.fi-body-has-sidebar-collapsible-on-desktop:not(.fi-body-has-top-navigation) .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item {
                                width: 100%;
                            }

                            .fi-body.fi-body-has-sidebar-collapsible-on-desktop:not(.fi-body-has-top-navigation) .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-group-items {
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                gap: 0.75rem;
                            }

                            .fi-body.fi-body-has-sidebar-collapsible-on-desktop:not(.fi-body-has-top-navigation) .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn,
                            .fi-body.fi-body-has-sidebar-collapsible-on-desktop:not(.fi-body-has-top-navigation) .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-open-collapse-sidebar-btn {
                                width: 2.5rem;
                                height: 2.5rem;
                                margin-inline: auto !important;
                                padding: 0.5rem !important;
                            }

                            .fi-body.fi-body-has-sidebar-collapsible-on-desktop:not(.fi-body-has-top-navigation) .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn {
                                gap: 0 !important;
                            }

                            .fi-body.fi-body-has-sidebar-collapsible-on-desktop:not(.fi-body-has-top-navigation) .fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-footer {
                                margin-inline: 0.25rem !important;
                            }
                        }
                    </style>
                    HTML)
            )
            ->colors([
                'primary' => Color::Sky,
            ])
            ->navigationGroups([
                '数据明细',
                '数据预警',
                '系统管理',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                StatsOverview::class,
                ActivityTrendChart::class,
                RevenueSpendTrendChart::class,
                RoiPayTrendChart::class,
                RetentionOverviewWidget::class,
                OpenAlertsWidget::class,
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
