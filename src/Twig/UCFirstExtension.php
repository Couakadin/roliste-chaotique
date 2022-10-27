<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UCFirstExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('UCFirst', [$this, 'UCFirst']),
        ];
    }

    public function UCFirst(string $string, string $encode = 'UTF-8'): ?string
    {
        $start = mb_strtoupper(mb_substr($string, 0, 1, $encode), $encode);
        $end = mb_substr($string, 1, mb_strlen($string, $encode), $encode);

        return $start.$end;
    }

    public function getName(): string
    {
        return 'UCFirst';
    }
}