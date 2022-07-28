<?php

namespace App\Twig;

use HTMLPurifier;
use HTMLPurifier_Config;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HTMLPurifierExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('html_purifier', [$this, 'purify'], ['is_safe' => ['html']]),
        ];
    }

    public function purify($text): string
    {
        $elements = [
            'p',
            'br',
            'small',
            'strong', 'b',
            'em', 'i',
            'strike',
            'sub', 'sup',
            'ins', 'del',
            'ol', 'ul', 'li',
            'h1', 'h2', 'h3',
            'dl', 'dd', 'dt',
            'pre', 'code', 'samp', 'kbd',
            'q', 'blockquote', 'abbr', 'cite',
            'table', 'thead', 'tbody', 'th', 'tr', 'td',
            'a[href|target|rel|id]',
            'img[src|title|alt|width|height|style]'
        ];

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', implode(',', $elements));

        $purifier = new HTMLPurifier($config);
        return $purifier->purify($text);
    }

    public function getName(): string
    {
        return 'html_purifier';
    }
}