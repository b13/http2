<?php
declare(strict_types = 1);
namespace B13\Http2\Tests\Unit;

/*
 * This file is part of TYPO3 CMS-based extension "http2" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */


use B13\Http2\ResourceMatcher;
use PHPUnit\Framework\TestCase;

class ResourceMatcherTest extends TestCase
{
    public function matchDataProvider()
    {
        return [
            'nothing useful' => [
                '<title>Good things must come</title>',
                [],
                []
            ],
            'simple script tag' => [
                '<script src="https://www.example.com/myfile.js" />',
                ['https://www.example.com/myfile.js'],
                []
            ],
            'simple script tag with parameter' => [
                '<script src="https://www.example.com/myfile.js?foo" />',
                ['https://www.example.com/myfile.js?foo'],
                []
            ],
            'simple script tag with parameter and value' => [
                '<script src="https://www.example.com/myfile.js?foo=bar" />',
                ['https://www.example.com/myfile.js?foo=bar'],
                []
            ],
            'multiple script tags' => [
                '<script src="https://www.example.com/myfile.js" /><link><script src="/myfile.js"></script>',
                ['https://www.example.com/myfile.js', '/myfile.js'],
                []
            ],
            'multiple script tags with the same value finds duplicate hits' => [
                '<script src="https://www.example.com/myfile.js" /><link><script src="/myfile.js"></script><script src="/myfile.js" />',
                ['https://www.example.com/myfile.js', '/myfile.js', '/myfile.js'],
                []
            ],
            'multiple script and link tags' => [
                '<script src="https://www.example.com/myfile.js" /><link href="http://example.com/favicon.ico"><script src="/myfile.js"></script>',
                ['https://www.example.com/myfile.js', '/myfile.js'],
                []
            ],
            'multiple script and valid link tags' => [
                '<script src="https://www.example.com/myfile.js" /><link href="http://example.com/base.css"><script src="/myfile.js"></script>',
                ['https://www.example.com/myfile.js', '/myfile.js'],
                ['http://example.com/base.css']
            ],
        ];
    }

    /**
     * @test
     * @dataProvider matchDataProvider
     */
    public function matchExtractsProperInformation($input, $expectedScripts, $expectedStyles)
    {
        $expectedResult = [
            'scripts' => $expectedScripts,
            'styles' => $expectedStyles
        ];
        $result = (new ResourceMatcher())->match($input);
        $this->assertEquals($expectedResult, $result);
    }
}
