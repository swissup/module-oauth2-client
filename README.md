# module-google-oauth2
Magento 2 module to provide OAuth 2.0 support for Google API Services.
The module's main purpose was to reuse the same code and credentials in other modules.

### Installation

###### For maintainers

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/module-google-oauth2 --prefer-source --ignore-platform-reqs
bin/magento module:enable Swissup_GoogleOAuth2 Swissup_Core
bin/magento setup:upgrade
bin/magento setup:di:compile
```
