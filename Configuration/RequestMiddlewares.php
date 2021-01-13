<?php
return [
    'frontend' => [
        'b13/http2/push-resources' => [
            'target' => \B13\Http2\Http\ResourcePusher::class,
            'before' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
            'after' => [
                'typo3/cms-frontend/authentication',
            ],
        ],
    ]
];
