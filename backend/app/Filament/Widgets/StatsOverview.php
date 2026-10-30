<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatusEnum;
use App\Filament\Widgets\Concerns\CountsPerMonth;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
	use CountsPerMonth;

	public static function canView(): bool
	{
		// Shop data is for admins only
		return auth()->user()?->hasRole('admin') ?? false;
	}

	protected static ?int $sort = 3;

	protected ?string $heading = 'Shop';

	protected ?string $pollingInterval = '15s';

	protected static bool $isLazy = true;

	protected function getStats(): array
	{
		$products = Product::count();
		$publishedProducts = Product::published()->count();
		// The status column stores lowercase values ('pending'), the enum has 'Pending'
		$pendingOrders = Order::where('status', strtolower(OrderStatusEnum::PENDING->value))->count();

		return [
			$this->customersStat(),
			Stat::make('Products', $products)
				->description("{$publishedProducts} of {$products} visible on the site")
				->color('warning')
				->chart(array_values($this->countPerMonth(Product::query(), 6))),
			Stat::make('Pending orders', $pendingOrders)
				->description(trans_choice('{0} No orders yet|{1} Out of 1 order|[2,*] Out of :count orders', Order::count()))
				->color($pendingOrders ? 'danger' : 'success')
				->chart(array_values($this->countPerMonth(Order::query(), 6))),
		];
	}

	// New customers in the last 30 days, compared with the 30 days before
	private function customersStat(): Stat
	{
		$last30Days = Customer::where('created_at', '>=', now()->subDays(30))->count();
		$previous30Days = Customer::whereBetween('created_at', [ now()->subDays(60), now()->subDays(30) ])->count();

		$stat = Stat::make('Customers', Customer::count())
			->color('success')
			->chart(array_values($this->countPerMonth(Customer::query(), 6)));

		if ( $last30Days === $previous30Days ) {
			return $stat->description("{$last30Days} new in the last 30 days");
		}

		return $stat
			->description("{$last30Days} new in the last 30 days (was {$previous30Days})")
			->descriptionIcon($last30Days > $previous30Days ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
			->color($last30Days > $previous30Days ? 'success' : 'danger');
	}
}
