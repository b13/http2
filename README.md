# HTTP/2 Pre-Fetch for TYPO3

[Latest Version on Packagist][link-packagist]
[Software License](LICENSE.md)
[Build Status][link-travis]

This TYPO3 extension fetches all CSS and JS resources that are used
for a page-rendering and sends additional HTTP/2 Headers "Link" for each resource
resulting in a faster first contentful paint for TYPO3 CMS.

This extension currently runs on TYPO3 v10, TYPO3 v11 and TYPO3 v12,
and needs PHP 7.4 or higher.

## Installation

Just use `composer req b13/http2` and install the extension via the Extension Manager,
flush caches and you're ready to go.

In order to see if the extension works, ensure that your webserver supports HTTP/2,
runs via HTTPS and check your Response headers to see if "link: " headers are added
to your HTTP response.

Nothing to configure, it just works(tm).

## Requirements

You need a webserver with HTTP/2 support, and - of course - HTTPS.

Also, use PHP7 - if you care about performance or supported PHP versions, there is nothing to discuss.


## How it works under the hood

1. Hook into the "PageRenderer" API class by fetching the concatenated CSS / JS files, and
libraries.
2. If in FE, this is stored within TSFE together with cached data (could be run twice here for non-cached info)
3. Send to the client via `header()` - in BE directly or in FE at the end of the request via a PSR-15 middleware (TYPO3 v10+ only).

## ToDo

* Implement options to also allow to define other resources (fonts/images), e.g. via TypoScript.
* Use proper DTOs instead of arrays.


## Credits

* [Benni Mack][link-author]

## License

As this is a PHP project, extending TYPO3, all code is licensed as GPL v2+.


[link-author]: https://github.com/bmack
[link-packagist]: https://packagist.org/packages/b13/http2
[link-travis]: https://travis-ci.org/b13/http2

## Sharing our expertise

[Find more TYPO3 extensions we have developed](https://b13.com/useful-typo3-extensions-from-b13-to-you) that help us deliver value in client projects. As part of the way we work, we focus on testing and best practices to ensure long-term performance, reliability, and results in all our code.
