<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\CountsPerMonth;
use App\Models\Product;
use Filament\Widgets\ChartWidget;

class ProductsChart extends ChartWidget
{
	use CountsPerMonth;

	public static function canView(): bool
	{
		// Shop data is for admins only
		return auth()->user()?->hasRole('admin') ?? false;
	}

	protected ?string $heading = 'Products added';

	protected ?string $description = 'Last 12 months';

	protected static ?int $sort = 4;

	protected function getData(): array
	{
		$productsPerMonth = $this->countPerMonth(Product::query());

		return [
			'datasets' => [
				[
					'label' => 'Products added',
					'data' => array_values($productsPerMonth),
				],
			],
			'labels' => array_keys($productsPerMonth),
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
		return 'line';
	}
}
