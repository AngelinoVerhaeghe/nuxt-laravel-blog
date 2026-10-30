<?php

namespace App\Providers\Filament;

use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Auth\AcceptInvitation;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;

class AdminPanelProvider extends PanelProvider
{
	public function boot(): void
	{
		// Filament's own dropdown instead of the browser's, so the option list can match the theme
		Select::configureUsing(fn (Select $select) => $select->native(false));
	}

	public function panel( Panel $panel ): Panel
	{
		return $panel
			->default()
			->id('dashboard')
			->path('dashboard')
			->login()
			->profile()
			->routes(function (): void {
				// Link from the invitation email (see User::sendInvitation())
				Route::get('/invitation/accept', AcceptInvitation::class)
					->middleware('signed')
					->name('auth.invitation.accept');
			})
			// Pastel palette, matching the frontend (frontend/app/assets/css/main.css)
			->colors([
				// lavender-deep as shade 600, and 500 (button hover) deep enough for white text too
				'primary' => array_replace(Color::hex('#6e58a8'), [
					500 => Color::convertToOklch('#765fb0'),
					600 => Color::convertToOklch('#6e58a8'),
					700 => Color::convertToOklch('#5b4890'),
				]),
				'success' => Color::hex('#3d7f63'), // mint-deep
				'danger' => Color::Rose,
				'warning' => Color::Orange,
				'info' => Color::Sky,
				'gray' => array_replace(Color::Stone, [
					50 => Color::convertToOklch('#fbf7f2'), // cream, the page background
					950 => Color::convertToOklch('#2f2a3b'), // ink, the text and avatar color
				]),
			])
			->font('Quicksand')
			->assets([
				Css::make('admin-theme', resource_path('css/filament/admin-theme.css')),
			])
			->globalSearchKeyBindings([ 'ctrl+k' ]) // Search Keybindings, control + k gets u to global search
			->sidebarCollapsibleOnDesktop() // Collapse sidebar
			->sidebarWidth('16rem')
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
				'logout' => MenuItem::make()->label('Log Out')// Customize Logout
			]) // Set a navigation item specific to a user, this will show up in the right corner on name icon
			//->breadcrumbs(false)	// Disable the breadcrumbs
			//->topNavigation() // Top Navigation instead of sidebar
			//->maxContentWidth('full') // Full width
			->favicon(asset('favicon.ico'))
			->plugins([
				SpotlightPlugin::make()
			])
			->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
			->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
			->pages([
				Dashboard::class,
			])
			->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
			])
			->plugin(FilamentSpatieRolesPermissionsPlugin::make());
	}
}
