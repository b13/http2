<?php
declare(strict_types = 1);
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
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

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
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if (($GLOBALS['TSFE'] ?? null) instanceof TypoScriptFrontendController) {
            $resources = $GLOBALS['TSFE']->config['b13/http2'] ?? null;
            /** @var NormalizedParams $normalizedParams */
            $normalizedParams = $request->getAttribute('normalizedParams');
            if (is_array($resources) && $normalizedParams->isHttps()) {
                foreach ($resources['scripts'] ?? []  as $resource) {
                    $response = $this->addPreloadHeaderToResponse($response, $resource, 'script');
                }
                foreach ($resources['styles'] ?? []  as $resource) {
                    $response = $this->addPreloadHeaderToResponse($response, $resource, 'style');
                }
            }
        }
        return $response;
    }

    protected function addPreloadHeaderToResponse(ResponseInterface $response, string $uri, string $type): ResponseInterface
    {
        if(str_contains($uri, '.mjs')) {
            return $response->withAddedHeader('Link', '<' . htmlspecialchars(PathUtility::getAbsoluteWebPath($uri)) . '>; rel=modulepreload; as=' . $type);
        } else {
            return $response->withAddedHeader('Link', '<' . htmlspecialchars(PathUtility::getAbsoluteWebPath($uri)) . '>; rel=preload; as=' . $type);
        }
    }
}
