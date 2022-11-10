<?php
defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess']['b13/http2'] = B13\Http2\PageRendererHook::class . '->accumulateResources';
