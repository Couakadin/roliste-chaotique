<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MonthStringExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('month_string', [$this, 'monthString'], ['is_safe' => ['html']]),
        ];
    }

    public function monthString(int $int): ?string
    {
        $months = [
            1  => 'january',
            2  => 'february',
            3  => 'march',
            4  => 'april',
            5  => 'may',
            6  => 'june',
            7  => 'july',
            8  => 'august',
            9  => 'september',
            10 => 'october',
            11 => 'november',
            12 => 'december'
        ];

        foreach ($months as $key => $month) {
            if ($int === $key) {
                return $month;
            }
        }

        return null;
    }

    public function getName(): string
    {
        return 'month_string';
    }
}