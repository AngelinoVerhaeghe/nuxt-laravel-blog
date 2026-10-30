<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrdersChart extends ChartWidget
{
	public static function canView(): bool
	{
		// Shop data is for admins only
		return auth()->user()?->hasRole('admin') ?? false;
	}

	protected static ?int $sort = 4;

	protected ?string $heading = 'Orders by status';

	protected ?string $description = 'All time';

	protected function getData(): array
	{
		// The status column stores lowercase values ('pending'), the enum has 'Pending'
		$counts = Order::select('status', DB::raw('count(*) as count'))
			->groupBy('status')
			->pluck('count', 'status')
			->mapWithKeys(fn ($count, string $status) => [ strtolower($status) => $count ]);

		// One bar per status, in enum order, so a status without orders shows as 0
		$statuses = collect(OrderStatusEnum::cases());

		return [
			'datasets' => [
				[
					'label' => 'Orders',
					'data' => $statuses->map(fn (OrderStatusEnum $status) => (int) $counts->get(strtolower($status->value), 0))->all(),
				],
			],
			'labels' => $statuses->map(fn (OrderStatusEnum $status) => $status->value)->all(),
		];
	}

	protected function getOptions(): array
	{
		return [
			'scales' => [ 'y' => [ 'beginAtZero' => true, 'ticks' => [ 'precision' => 0 ] ] ],
		];
	}

	protected function getType(): string
	{
		return 'bar';
	}
}
