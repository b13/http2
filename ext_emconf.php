<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'HTTP2 Server Push Support for TYPO3',
    'description' => '',
    'category' => 'extension',
    'version' => '2.0.0',
    'state' => 'stable',
    'clearcacheonload' => 1,
    'author' => 'b13 GmbH',
    'author_email' => 'typo3@b13.com',
    'author_company' => 'b13 GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-12.99.99',
        ],
    ],
];
