<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

// Values match the `status` column of the orders table
enum OrderStatusEnum : string implements HasLabel, HasColor
{
	case PENDING = 'pending';
	case PROCESSING = 'processing';
	case COMPLETED = 'completed';
	case DECLINED = 'declined';

	public function getLabel(): string
	{
		return ucfirst($this->value);
	}

	public function getColor(): string
	{
		return match ( $this ) {
			self::PENDING => 'warning',
			self::PROCESSING => 'info',
			self::COMPLETED => 'success',
			self::DECLINED => 'danger',
		};
	}
}
