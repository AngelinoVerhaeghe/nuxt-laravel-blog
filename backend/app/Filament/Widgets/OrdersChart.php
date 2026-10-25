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

	protected static ?int $sort = 3;

	protected ?string $heading = 'Chart';

	protected function getData(): array
	{
		$data = Order::select('status', DB::raw('count(*) as count'))
			->groupBy('status')
			->pluck('count', 'status')
			->toArray();

		return [
			'datasets' => [
				[
					'label' => 'Orders',
					'data' => array_values($data)
				]
			],
			'labels' => OrderStatusEnum::cases()
		];
	}

	protected function getType(): string
	{
		return 'bar';
	}
}
