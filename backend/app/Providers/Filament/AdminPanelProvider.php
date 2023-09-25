<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;

class AdminPanelProvider extends PanelProvider
{
	public function panel( Panel $panel ): Panel
	{
		return $panel
			->default()
			->id('dashboard')
			->path('dashboard')
			->login()
			->colors([
				'primary' => Color::Lime,
			])
			->globalSearchKeyBindings([ 'ctrl+k' ]) // Search Keybindings, control + k gets u to global search
			->sidebarCollapsibleOnDesktop() // Collapse sidebar
			//->sidebarFullyCollapsibleOnDesktop() // Collapse sidebar as default, use the method
			->navigationItems([
				NavigationItem::make('RSC Anderlecht')
				->url('https://rsca.be', shouldOpenInNewTab: true)
				->icon('heroicon-o-pencil-square')
				->group('External')
				->sort(4)
				//->visible(fn(): bool => auth()->user()->can('view')) // View permission on authenticated user
			])	// External links
			->userMenuItems([
				MenuItem::make()
				->label('Settings')
				->url('')
				->icon('heroicon-o-cog-6-tooth'),
				'logout' => MenuItem::make()->label('Log Out')// Customize Logout
			]) // Set a navigation item specific to a user, this will show up in the right corner on name icon
			//->breadcrumbs(false)	// Disable the breadcrumbs
			//->topNavigation() // Top Navigation instead of sidebar
			//->maxContentWidth('full') // Full width
			->font('Dunbar')
			->favicon('')
			->plugins([
				SpotlightPlugin::make()
			])
			->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
			->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
			->pages([
				Pages\Dashboard::class,
			])
			->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
			->widgets([
				Widgets\AccountWidget::class,
				Widgets\FilamentInfoWidget::class,
			])
			->middleware([
				EncryptCookies::class,
				AddQueuedCookiesToResponse::class,
				StartSession::class,
				AuthenticateSession::class,
				ShareErrorsFromSession::class,
				VerifyCsrfToken::class,
				SubstituteBindings::class,
				DisableBladeIconComponents::class,
				DispatchServingFilamentEvent::class,
			])
			->authMiddleware([
				Authenticate::class,
			])
			->plugin(FilamentSpatieRolesPermissionsPlugin::make());
	}
}
