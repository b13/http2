<?php

namespace B13\Http2\Tests\Functional;

/*
 * This file is part of TYPO3 CMS-based extension "http2" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ResponseTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['typo3conf/ext/http2'];
    protected array $coreExtensionsToLoad = ['core', 'frontend'];
    protected array $pathsToLinkInTestInstance = ['typo3conf/ext/http2/Build/sites' => 'typo3conf/sites'];

    #[Test]
    public function linkHeaderExists(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Response.csv');
        $request = new InternalRequest('http://localhost/');
        $request = $request->withServerParams(['HTTPS' => 'on']);
        $response = $this->executeFrontendSubRequest($request);
        self::assertTrue($response->hasHeader('link'));
        $link = $response->getHeaderLine('link');
        self::assertStringContainsString('JavaScript/default_frontend.js', $link);
        self::assertStringContainsString('>; rel=preload; as=script', $link);
        // cached
        $response = $this->executeFrontendSubRequest($request);
        self::assertTrue($response->hasHeader('link'));
        $link = $response->getHeaderLine('link');
        self::assertStringContainsString('JavaScript/default_frontend.js', $link);
        self::assertStringContainsString('>; rel=preload; as=script', $link);
    }
}
