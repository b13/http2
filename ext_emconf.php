<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'HTTP2 Server Push Support for TYPO3',
    'description' => '',
    'category' => 'extension',
    'version' => '3.0.0',
    'state' => 'stable',
    'clearcacheonload' => 1,
    'author' => 'b13 GmbH',
    'author_email' => 'typo3@b13.com',
    'author_company' => 'b13 GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.99.99',
        ],
    ],
];
