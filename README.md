# Magento LiveChat Module

This module adds [LiveChat](https://www.livechatinc.com/) to your Magento 2 site.

### Installation

* `composer require livechat/module-magento-livechat:dev-master`
* `php bin/magento module:enable LiveChat_LiveChat`
* `php bin/magento setup:upgrade`
* `php bin/magento setup:static-content:deploy`
* `php bin/magento cache:clean`

### Features

* Connecting existing LiveChat account,
* Creating new LiveChat account,
* Injecting LiveChat JS snippet,
* Sending customers details to LiveChat,
* Sending customers cart details to LiveChat,
* Sending customers last order details to LiveChat,
* Sending customers last order details to LiveChat,
* Set-up [goal](https://www.livechatinc.com/kb/goals-set-up-and-use/) for order placed event.
