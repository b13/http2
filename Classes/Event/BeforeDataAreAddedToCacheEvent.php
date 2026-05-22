<?php

declare(strict_types=1);

namespace B13\Http2\Event;

/*
 * This file is part of TYPO3 CMS-based extension "http2" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Psr\Http\Message\ServerRequestInterface;

final class BeforeDataAreAddedToCacheEvent
{
    public function __construct(
        public readonly ServerRequestInterface $request,
        public array $cacheTags,
    ) {}
}
