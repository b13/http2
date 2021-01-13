<?php
defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess']['b13/http2'] = B13\Http2\PageRendererHook::class . '->accumulateResources';

// can be removed as soon as TYPO3 v9 support is dropped, as this is now taken care of by a middleware
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output']['b13/http2'] = B13\Http2\ResourcePusher::class . '->pushForFrontend';
