<?php

defined('TYPO3') || die('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'LexofficeApi',
    'Invoice',
    [
        \Passionweb\LexofficeApi\Controller\LexofficeController::class => 'invoice, download'
    ],
    // non-cacheable actions
    [
        \Passionweb\LexofficeApi\Controller\LexofficeController::class => 'invoice, download'
    ]
);

// wizards
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    'mod {
        wizards.newContentElement.wizardItems.plugins {
            elements {
                invoice {
                    iconIdentifier = lexoffice-invoice
                    title = LLL:EXT:lexoffice_api/Resources/Private/Language/locallang_db.xlf:plugin_lexoffice_invoice.name
                    description = LLL:EXT:lexoffice_api/Resources/Private/Language/locallang_db.xlf:plugin_lexoffice_invoice.description
                    tt_content_defValues {
                        CType = list
                        list_type = lexofficeapi_invoice
                    }
                }
            }
            show = *
        }
   }'
);

$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
    'lexoffice-invoice',
    \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
    ['source' => 'EXT:lexoffice_api/Resources/Public/Icons/Extension.png']
);


