<?php

namespace App\Enums;

enum ProductTypeEnum: string
{
	case POP = 'Pop!';
	case PINS = 'Pins';
	case MINI_FIGURES = 'Mini Figures';
	case GAMES = 'Games';
	case ACCESSORIES = 'Accessories';
}
