<?php

namespace App\Filament\Widgets\Concerns;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

trait CountsPerMonth
{
	/**
	 * Records created in each of the last $months months, oldest first, keyed by a 'M Y' label.
	 * The current month is included, so the last value can still grow.
	 *
	 * @return array<string, int>
	 */
	protected function countPerMonth( Builder $query, int $months = 12 ): array
	{
		$start = CarbonImmutable::now()->startOfMonth()->subMonths($months - 1);

		$counts = $query->clone()
			->where('created_at', '>=', $start)
			->pluck('created_at')
			->countBy(fn ($createdAt) => $createdAt->format('Y-m'));

		$result = [];

		for ( $month = $start; $month->isPast(); $month = $month->addMonth() ) {
			$result[$month->format('M Y')] = $counts->get($month->format('Y-m'), 0);
		}

		return $result;
	}
}
