<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class BaseNameExtension extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getFilters()
    {
        return [
            new TwigFilter('basename', [$this, 'basename']),
        ];
    }

    /**
     * @param $path
     * @param string $suffix
     * @return string
     */
    public function basename($path, $suffix = null)
    {
        return basename($path, $suffix);
    }
}
