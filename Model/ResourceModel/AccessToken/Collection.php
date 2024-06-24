<?php
namespace Swissup\OAuth2Client\Model\ResourceModel\AccessToken;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Swissup\OAuth2Client\Model\AccessToken;
use Swissup\OAuth2Client\Model\ResourceModel\AccessToken as AccessTokenResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(AccessToken::class, AccessTokenResource::class);
    }
}
