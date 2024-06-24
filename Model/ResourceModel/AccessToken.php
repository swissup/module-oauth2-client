<?php
namespace Swissup\OAuth2Client\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AccessToken extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('swissup_oauth_client_access_token', 'id');
    }
}
