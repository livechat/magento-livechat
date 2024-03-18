### Local development

We recommend to use Mark Shust's Docker Configuration for Magento.
You can find it [here](https://github.com/markshust/docker-magento).

#### 2FA

The latest Magento version introduces obligatory 2FA for admin panel.
You can disable it by using the `DisableTwoFactorAuth` module which provides the ability to disable it by defualt for dev env.
You can find it [here](https://github.com/markshust/magento2-module-disabletwofactorauth).

#### Mounting local module

Thea easiest way to develop the module is to mount it to the `vendor` directory.
Firstly, you need to install LiveChat module via composer:
```bash
composer require livechat/module-magento-livechat:@dev
# or if you use magento-docker by Mark Shust
bin/composer require livechat/module-magento-livechat:@dev
```
The `@dev` version will accept any version of the module.

Then, you need to mount the module to the `vendor` directory.
If you use `magento-docker` by Mark Shust, you can edit the `compse.dev.yaml` file like below:
```yaml
services:
  app:
    volumes: &appvolumes
      ## Host mounts with performance penalty, only put what is necessary here
      - ./src/app/code:/var/www/html/app/code:cached
      # ...
      - /path/to/the/magento-livechat:/var/www/html/vendor/livechat/module-magento-livechat:cached
```

From now, you can follow the official help article for LiveChat module [here](https://www.livechat.com/help/magento-integration-guide/#installation2) and continue the installation process.
