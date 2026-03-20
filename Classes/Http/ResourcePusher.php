<?php

declare(strict_types=1);

namespace B13\Http2\Http;

/*
 * This file is part of TYPO3 CMS-based extension "http2" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\Core\Cache\CacheDataCollector;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Takes existing accumulated resources and pushes them as HTTP2 <link> headers as middleware.
 *
 * This considers that everything is required, thus is marked as "preload", not via "prefetch".
 *
 * Also, it only tackles "script", "style", but we should incorporate font as well.
 * See https://w3c.github.io/preload/#as-attribute
 */
class ResourcePusher implements MiddlewareInterface
{
    public function __construct(
        #[Autowire(service: 'cache.tx_http2')]
        private readonly FrontendInterface $cache
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $response = $response->withHeader('foo', 'bar');

        /** @var CacheDataCollector $cacheDataCollector */
        $cacheDataCollector = $request->getAttribute('frontend.cache.collector');
        $identifier = $cacheDataCollector->getPageCacheIdentifier();
        $resources = [];
        if ($this->cache->has($identifier)) {
            $resources = $this->cache->get($identifier);
        }

        /** @var NormalizedParams $normalizedParams */
        $normalizedParams = $request->getAttribute('normalizedParams');
        if (!empty($resources) && $normalizedParams->isHttps()) {
            foreach ($resources['scripts'] ?? [] as $resource) {
                $response = $this->addPreloadHeaderToResponse($response, $resource, 'script');
            }
            foreach ($resources['styles'] ?? [] as $resource) {
                $response = $this->addPreloadHeaderToResponse($response, $resource, 'style');
            }
        }
        return $response;
    }

    protected function addPreloadHeaderToResponse(ResponseInterface $response, string $uri, string $type): ResponseInterface
    {
        if (str_contains($uri, '.mjs')) {
            return $response->withAddedHeader('Link', '<' . htmlspecialchars(PathUtility::getAbsoluteWebPath($uri)) . '>; rel=modulepreload; as=' . $type);
        }
        return $response->withAddedHeader('Link', '<' . htmlspecialchars(PathUtility::getAbsoluteWebPath($uri)) . '>; rel=preload; as=' . $type);

    }
}
