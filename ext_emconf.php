<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Lexoffice invoices within TYPO3',
    'description' => 'Shows the integration of the Lexoffice API.',
    'category' => 'service',
    'author' => 'Manuel Schnabel',
    'author_email' => 'service@passionweb.de',
    'author_company' => 'PassionWeb Manuel Schnabel',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => ['typo3' => '11.5.0-12.4.99'],
        'conflicts' => [],
        'suggests' => [],
    ],
];
