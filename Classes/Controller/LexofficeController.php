<?php

namespace Passionweb\LexofficeApi\Controller;


use Passionweb\LexofficeApi\Service\LexofficeService;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class LexofficeController extends ActionController
{
    protected array $extConf;

    protected string $invoicePath;

    protected LexofficeService $lexofficeService;

    protected LoggerInterface $logger;

    protected ExtensionConfiguration $extensionConfiguration;

    protected array $data = [];

    public function __construct(
        LexofficeService $lexofficeService
    )
    {
        $this->lexofficeService = $lexofficeService;

        $this->invoicePath = __DIR__. '/../../Documents/Invoices/';

        // replace this with your own data
        $this->data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'street' => 'Main Street 1',
            'zip' => '12345',
            'city' => 'Berlin',
            'countryCode' => 'DE',
            'product' => [
                'productNumber' => '10000',
                'title' => 'My Product',
                'description' => 'My Product Description',
                'price' => 99.99,
                'quantity' => 3,
            ]
        ];
    }

    public function invoiceAction(): ResponseInterface
    {
        $invoiceId = $this->lexofficeService->createAndGetInvoice($this->data);
        $this->view->assign('invoiceId', $invoiceId);
        return $this->htmlResponse();
    }

    public function downloadAction()
    {
        // add further validations/permission checks before downloading the invoice
        $invoiceId = $this->request->getArgument('invoiceId');
        $invoicePath = $this->invoicePath . $invoiceId . '.pdf';

        header("Content-Type:application/pdf");
        header("Content-Disposition:attachment;filename=\"invoice.pdf\"");
        readfile($invoicePath);
        exit;
    }
}
