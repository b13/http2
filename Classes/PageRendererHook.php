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

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Hooks into PageRenderer before the rendering is taken care of, and remember the files
 * that can be sent as resources.
 *
 * This considers that everything is required, thus is marked as "preload", not via "prefetch".
 */
class PageRendererHook
{
    /**
     * @var ResourceMatcher
     */
    protected $matcher;

    /**
     * @param ResourceMatcher|null $matcher
     */
    public function __construct(ResourceMatcher $matcher = null)
    {
        $this->matcher = $matcher ?: GeneralUtility::makeInstance(ResourceMatcher::class);
    }

    /**
     * @param array $params
     * @param PageRenderer $pageRenderer
     * @internal this is just a hook implementation.
     */
    public function accumulateResources(array $params, PageRenderer $pageRenderer)
    {
        // If this is a second run (non-cached cObjects adding more data), then the existing cached data is fetched
        if ($this->getTypoScriptFrontendController() instanceof TypoScriptFrontendController) {
            $allResources = $this->getTypoScriptFrontendController()->config['b13/http2'] ?? [];
        } else {
            $allResources = [];
        }
        foreach ($params['headerData'] as $headerData) {
            $allResources = array_merge_recursive($allResources, $this->matcher->match($headerData));
        }
        foreach ($params['footerData'] as $footerData) {
            $allResources = array_merge_recursive($allResources, $this->matcher->match($footerData));
        }
        foreach (['jsLibs', 'jsFiles', 'jsFooterLibs', 'jsFooterFiles', 'cssLibs', 'cssFiles'] as $part) {
            if (empty($params[$part])) {
                continue;
            }
            $allResources = array_merge_recursive($allResources, $this->matcher->match($params[$part]));
        }

        $allResources['scripts'] = array_unique($allResources['scripts'] ?? []);
        $allResources['styles'] = array_unique($allResources['styles'] ?? []);

        $this->process($allResources);
    }

    /**
     * In FE the data is stored in TSFE->config.
     * In BE it is sent directly to the HTTP response.
     *
     * @param array $allResources
     */
    protected function process(array $allResources)
    {
        if ($this->getTypoScriptFrontendController() instanceof TypoScriptFrontendController) {
            $this->getTypoScriptFrontendController()->config['b13/http2'] = $allResources;
        } elseif (GeneralUtility::getIndpEnv('TYPO3_SSL')) {
            // Push directly into the TYPO3 Backend, but only if TYPO3 is running in SSL
            GeneralUtility::makeInstance(ResourcePusher::class)->pushAll($allResources);
        }
    }

    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
