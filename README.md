# module-oauth2-client
Magento 2 module to provide OAuth 2.0 support.
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


#### Docs

- [original issue](https://github.com/swissup/module-email/issues/31)
- [Beginning September 30, 2024: third-party apps that use only a password to access Google Accounts and Google Sync will no longer be supported](https://workspaceupdates.googleblog.com/2023/09/winding-down-google-sync-and-less-secure-apps-support.html)
- [thephpleague/oauth2-google](https://github.com/thephpleague/oauth2-google?tab=readme-ov-file)
- [Providers](https://oauth2-client.thephpleague.com/providers/thirdparty/)
- [Official Provider Clients](https://oauth2-client.thephpleague.com/providers/league/)
- [Add support for Microsoft POP3 XOAUTH2](https://github.com/laminas/laminas-mail/commit/64b2059bd25186ceca5ec5d8802ce35d8d9ad0e6)
- [Project Credentials](https://console.cloud.google.com/apis/credentials?project=swissup-email-gmail-oauth2)
- [https://support.google.com/cloud/answer/10311615#zippy=%2Ctesting](https://support.google.com/cloud/answer/10311615#zippy=%2Ctesting)
- [Set Up Email Importing with OAuth2 and Google ](https://docs.whmcs.com/support/support-tutorials/set-up-email-importing-google/)
- ['Token has been expired or revoked'](https://stackoverflow.com/questions/66058279/token-has-been-expired-or-revoked-google-oauth2-refresh-token-gets-expired-i)
- [Gmail IMAP OAuth2 returns error code 400](https://stackoverflow.com/questions/13465349/gmail-imap-oauth2-returns-error-code-400)

