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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Takes existing accumulated resources and pushes them as HTTP2 <link> headers.
 *
 * This considers that everything is required, thus is marked as "preload", not via "prefetch".
 *
 * Also, it only tackles "script", "style", but we should incorporate font as well.
 * See https://w3c.github.io/preload/#as-attribute
 */
class ResourcePusher
{
    /**
     * @param array $resources
     */
    public function pushAll(array $resources)
    {
        foreach ($resources['scripts'] as $resource) {
            $this->addPreloadHeader($resource, 'script');
        }
        foreach ($resources['styles'] as $resource) {
            $this->addPreloadHeader($resource, 'style');
        }
    }

    /**
     * @param array $params
     * @param TypoScriptFrontendController $typoScriptFrontendController
     * @internal this is just a hook implementation.
     */
    public function pushForFrontend(array $params, TypoScriptFrontendController $typoScriptFrontendController)
    {
        $allResources = $typoScriptFrontendController->config['b13/http2'];
        if (GeneralUtility::getIndpEnv('TYPO3_SSL') && is_array($allResources)) {
            $this->pushAll($allResources);
        }
    }

    /**
     * as="{style/script/image}"
     *
     * @param string $uri
     * @param string $type
     */
    protected function addPreloadHeader(string $uri, string $type)
    {
        header('Link: <' . htmlspecialchars(PathUtility::getAbsoluteWebPath($uri)) . '>; rel=preload; as=' . $type, false);
    }
}
