<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\CountsPerMonth;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogStatsOverview extends BaseWidget
{
	use CountsPerMonth;

	protected static ?int $sort = 1;

	protected ?string $heading = 'Blog';

	protected function getStats(): array
	{
		$published = Post::published();
		$publishedThisMonth = $published->clone()->where('created_at', '>=', now()->startOfMonth())->count();
		$drafts = Post::where('is_published', false)->count();

		return [
			Stat::make('Published posts', $published->count())
				->description(trans_choice('{0} No new posts this month|{1} 1 new post this month|[2,*] :count new posts this month', $publishedThisMonth))
				->color('primary')
				->chart(array_values($this->countPerMonth($published, 6))),
			Stat::make('Drafts', $drafts)
				->description($drafts ? 'Not on the site yet' : 'Everything is published')
				->color($drafts ? 'warning' : 'success'),
			Stat::make('Categories', Category::count())
				->description(trans_choice('{0} No tags yet|{1} 1 tag|[2,*] :count tags', Tag::count())),
		];
	}
}
