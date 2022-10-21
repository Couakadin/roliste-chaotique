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

    public function UCFirst(string $string): ?string
    {
        return ucfirst($string);
    }

    public function getName(): string
    {
        return 'UCFirst';
    }
}