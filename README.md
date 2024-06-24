# module-oauth2-client
Magento 2 module to provide OAuth 2.0 supports.
The module's main purpose was to reuse the same code and credentials in other modules.

### Installation

###### For maintainers

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/module-oauth2-client:dev-master --prefer-source --ignore-platform-reqs --update-no-dev
bin/magento module:enable Swissup_OAuth2Client Swissup_Core
bin/magento setup:upgrade
bin/magento setup:di:compile
```
