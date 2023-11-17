# Use Lexoffice API for payments

Shows the integration of the Lexoffice API for invoice generation. (TYPO3 CMS)

## What does it do?

Adds a plugin to show the process for generating invoices with the Lexoffice API.

## Installation

Add via composer:

    composer require "passionweb/lexoffice-api"

* Install the extension via composer
* Flush TYPO3 and PHP Cache

## Requirements

This example uses the Lexoffice API and you need a private API key which can be generated here [https://app.lexoffice.de/addons/public-api](mailto:service@passionweb.de "Generate private API key") .

## Extension settings

There are the following extension settings available.

### `lexofficeActive`

    # cat=LexOffice; type=boolean; label=Activate Lexoffice
    lexofficeActive = false

### `lexofficeMode`

    # cat=LexOffice; type=string; label=Lexoffice mode (dev/live)
    lexofficeMode = dev

### `lexofficeApiUrl`

    # cat=LexOffice; type=string; label=Lexoffice API url
    lexofficeApiUrl =

### `lexofficeDevApiKey`

    # cat=LexOffice; type=string; label=Lexoffice Api key (test)
    lexofficeDevApiKey =

### `lexofficeLiveApiKey`

    # cat=LexOffice; type=string; label=Lexoffice Api key (live)
    lexofficeLiveApiKey =

Enter the page id of your payment success page.

## Troubleshooting and logging

If something does not work as expected take a look at the log file.
Every problem is logged to the TYPO3 log (normally found in `var/log/typo3_*.log`)

## Achieving more together or Feedback, Feedback, Feedback

I'm grateful for any feedback! Be it suggestions for improvement, requests or just a (constructive) feedback on how good or crappy this snippet/repo is.

Feel free to send me your feedback to [service@passionweb.de](mailto:service@passionweb.de "Send Feedback") or [contact me on Slack](https://typo3.slack.com/team/U02FG49J4TG "Contact me on Slack")
