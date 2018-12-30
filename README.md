# HTTP/2 Pre-Fetch for TYPO3

[![Latest Version on Packagist]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]

This TYPO3 extension fetches all CSS and JS resources that are used
for a page-rendering and sends additional HTTP/2 Headers "Link" for each resource
resulting in a faster first contentful paint for TYPO3 CMS.

This extension currently runs on TYPO3 v7 LTS, TYPO3 v8 LTS and TYPO3 v9 LTS.

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
3. Send to the client via `header()` - in BE directly or in FE at the end of the request.

## ToDo

* Use Middlewares and PSR-7 for TYPO3 v9
* Implement options to also allow to define other resources (fonts/images), e.g. via TypoScript.
* Use proper DTOs instead of arrays.


## Credits

* [Benni Mack][link-author]

## License

As this is a PHP project, extending TYPO3, all code is licensed as GPL v2+.


[link-packagist]: https://packagist.org/packages/b13/http2
[link-travis]: https://travis-ci.org/b13/http2