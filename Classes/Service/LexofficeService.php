<?php

namespace Passionweb\LexofficeApi\Service;

use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Log\LogManager;

class LexofficeService
{
    protected array $extConf;

    protected string $invoicePath;
    protected string $lexOfficeApiKey;

    protected LoggerInterface $logger;

    protected ExtensionConfiguration $extensionConfiguration;

    public function __construct(
        LoggerInterface $logger,
        ExtensionConfiguration $extensionConfiguration
    ) {
        $this->logger = $logger;
        $this->extensionConfiguration = $extensionConfiguration;

        $this->extConf = $this->extensionConfiguration->get('lexoffice_api');
        $this->invoicePath = __DIR__. '/../../Documents/Invoices/';
        $this->lexOfficeApiKey = $this->extConf['lexofficeMode'] === 'live' ? $this->extConf['lexofficeLiveApiKey'] : $this->extConf['lexofficeDevApiKey'];
    }


    public function createAndGetInvoice(array $data) {

        $jsonParams = '{
 "archived": false,
  "voucherDate": "'.date("Y-m-d").'T00:00:00.000+01:00",
   "address": {
    "name": "' . $data['firstname'] . ' ' . $data['lastname'] . '",
    "street": "' . $data['street'] . '",
    "city": "' . $data['city'] . '",
    "zip": "' . $data['zip'] . '",
    "countryCode": "'. $data['countryCode'] . '"
  },
  "lineItems": [
    {
      "type": "custom",
      "name": "' . $data['product']['title'] . '",
      "quantity": "' . $data['product']['quantity'] . '",
      "unitName": "Piece",
      "unitPrice": {
        "currency": "EUR",
        "netAmount": ' . number_format($data['product']['price'],2) . ',
        "grossAmount": ' . number_format($data['product']['price'],2) . ',
        "taxRatePercentage": 0
      },
      "discountPercentage": 0
    },
    {
      "type": "text",
      "name": "Product details",
      "description": "' . $data['product']['description'] . '"
    }';

        $jsonParams .= '
],
  "totalPrice": {
    "currency": "EUR"
   },
  "taxConditions": {
    "taxType": "gross"
  },
  "paymentConditions": {
    "paymentTermLabel": "Please transfer the amount to the following account within 10 days:\n\nAccount holder: HOLDER.\nIBAN: DEXX XXXX XXXX XXXX XXXX XX\nBIC: XXXXXXXXX\n\n",
    "paymentTermDuration": 10,
    "paymentDiscountConditions": {
      "discountPercentage": 0,
      "discountRange": 0
    }
  },
  "shippingConditions": {
    "shippingDate": "'.date("Y-m-d", time()).'T00:00:00.000+01:00",
    "shippingType": "delivery"
  },
  "title": "Invoice",
  "introduction": "Below we will invoice you for the services listed.",
  "remark": "Thank you for your order"
}';

        try {
            //create and save invoice
            $invoiceData = $this->generateInvoice($jsonParams);
            if(isset($invoiceData->error)) {
                throw new Exception($invoiceData->message.': '.$invoiceData->details[0]->field.', '.$invoiceData->details[0]->message, 123143247);
            }

            $invoiceDocData = $this->getInvoiceDocumentId($invoiceData->id);
            if(isset($invoiceData->error)) {
                throw new Exception($invoiceDocData->message, 123143248);
            }

            $pdfIssues = $this->saveInvoiceAsPdf($invoiceDocData->documentFileId, $invoiceData->id);

            if($pdfIssues) {
                throw new Exception($pdfIssues->type .': '.$pdfIssues->source, 123143249);
            }

            return $invoiceData->id;

        } catch(\Exception $e) {
            $this->logger->error("Lexoffice invoice creation failed!", array("Errormessage" => $e->getMessage()));
            return false;
        }
    }

    private function generateInvoice(string $jsonParams) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->extConf['lexofficeApiUrl'] . "invoices?finalize=true");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $this->lexOfficeApiKey
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $json = curl_exec($ch);
        $invoiceData = json_decode($json);
        curl_close($ch);

        return $invoiceData;
    }

    private function getInvoiceDocumentId(string $invoiceId) {
        //get invoice document id
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->extConf['lexofficeApiUrl'] . "invoices/" . $invoiceId . "/document");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Authorization: Bearer ' . $this->lexOfficeApiKey
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $json = curl_exec($ch);
        $invoiceDocData = json_decode($json);
        curl_close($ch);

        return $invoiceDocData;
    }

    private function saveInvoiceAsPdf(string $docFileId, string $invoiceId) {
        //get invoice as pdf
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->extConf['lexofficeApiUrl'] . "files/" . $docFileId);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: */*',
            'Authorization: Bearer ' . $this->lexOfficeApiKey
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $invoicePdf = curl_exec($ch);
        $invoicePdfData = json_decode($invoicePdf);
        curl_close($ch);

        if($invoicePdfData) {
            return $invoicePdfData;
        }
        else {
            //save file to invoices directory
            file_put_contents($this->invoicePath . $invoiceId . ".pdf", $invoicePdf);
            return false;
        }
    }
}
