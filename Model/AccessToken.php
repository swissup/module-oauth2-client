<?php
namespace Swissup\OAuth2Client\Model;

use Swissup\OAuth2Client\Api\Data\AccessTokenInterface;
use Magento\Framework\Model\AbstractModel;

class AccessToken extends AbstractModel implements AccessTokenInterface
{
    protected function _construct()
    {
        $this->_init(\Swissup\OAuth2Client\Model\ResourceModel\AccessToken::class);
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getProvider()
    {
        return $this->getData(self::PROVIDER);
    }

    public function getCredentialHash()
    {
        return $this->getData(self::CREDENTIAL_HASH);
    }

    public function getAccessToken()
    {
        return $this->getData(self::ACCESS_TOKEN);
    }

    public function getRefreshToken()
    {
        return $this->getData(self::REFRESH_TOKEN);
    }

    public function getExpires()
    {
        return $this->getData(self::EXPIRES);
    }

    public function getResourceOwnerId()
    {
        return $this->getData(self::RESOURCE_OWNER_ID);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function setProvider($provider)
    {
        return $this->setData(self::PROVIDER, $provider);
    }

    public function setCredentialHash($credentialHash)
    {
        return $this->setData(self::CREDENTIAL_HASH, $credentialHash);
    }

    public function setAccessToken($accessToken)
    {
        return $this->setData(self::ACCESS_TOKEN, $accessToken);
    }

    public function setRefreshToken($refreshToken)
    {
        return $this->setData(self::REFRESH_TOKEN, $refreshToken);
    }

    public function setExpires($expires)
    {
        return $this->setData(self::EXPIRES, $expires);
    }

    public function setResourceOwnerId($resourceOwnerId)
    {
        return $this->setData(self::RESOURCE_OWNER_ID, $resourceOwnerId);
    }
}
