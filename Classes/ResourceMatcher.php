<?php
declare(strict_types = 1);
namespace B13\Http2;

/*
 * This file is part of TYPO3 CMS-based extension "http2" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

/**
 * Parses a string by detecting <script> and <link> tags.
 */
class ResourceMatcher
{
    protected $resourcePattern = '[\'"]?([\w\s\/\=\-:?.]*)["\']?';

    /**
     * @param string $input
     * @return array
     */
    public function match(string $input): array
    {
        if (empty($input)) {
            return ['scripts' => [], 'styles' => []];
        }
        $matches = [];
        preg_match_all(
            $this->getPatternForCurrentPhpVersion(),
            $input,
            $matches
        );

        // simple check - should be optimized to include further checks
        $styles = array_filter($matches[2], function ($resource) {
            return strpos($resource, '.css') !== false;
        });

        return [
            'scripts' => array_values(array_filter($matches[1])),
            'styles' => array_values($styles)
        ];
    }

    /**
     * @return string
     */
    protected function getPatternForCurrentPhpVersion(): string
    {
        if (version_compare(phpversion(), '7.3', '>')) {
            return '/<script[\/\s\w\-="]* src=' . $this->resourcePattern . '[^>]*>'
                . '|' .
                '<link[\/\s\w\-="]*href=' . $this->resourcePattern . '[^>]*>'
                . '/ui';
        } else {
            return '/<script[\/\s\w-="]* src=' . $this->resourcePattern . '[^>]*>'
                . '|' .
                '<link[\/\s\w-="]*href=' . $this->resourcePattern . '[^>]*>'
                . '/ui';
        }
    }
}
