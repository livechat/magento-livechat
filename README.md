# Magento LiveChat Module

This module adds [LiveChat](https://www.livechat.com/) to your Magento 2 store.

## Requirements

* Magento 2.4.6 or newer
* PHP 8.1 or newer

## Installation

```bash
composer require livechat/module-magento-livechat
php bin/magento module:enable LiveChat_LiveChat
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
php bin/magento cache:clean
```

## Setup

In the Magento admin go to **Stores > Configuration > LiveChat**, then connect your existing LiveChat account or create a new one.

## Features

* Connecting an existing LiveChat account
* Creating a new LiveChat account
* Injecting the LiveChat JS snippet into the storefront
* Sending customer details to LiveChat
* Sending customer cart details to LiveChat
* Sending customer last order details to LiveChat
* Setting up a [goal](https://www.livechat.com/kb/goals-set-up-and-use/) for the order placed event

## License

See [LICENSE.txt](LICENSE.txt) and [LICENSE_AFL.txt](LICENSE_AFL.txt).
