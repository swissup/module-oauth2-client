<?php
namespace Swissup\OAuth2Client\Model;

use Swissup\OAuth2Client\Api\Data\CredentialInterface;
use Magento\Framework\DataObject;

class Credential extends DataObject implements CredentialInterface
{

    public function getClientId()
    {
        return $this->getData(self::CLIENT_ID);
    }

    public function getClientSecret()
    {
        return $this->getData(self::CLIENT_SECRET);
    }

    public function getScope()
    {
        return $this->getData(self::SCOPE);
    }

    public function setClientId($id): Credential
    {
        return $this->setData(self::CLIENT_ID, $id);
    }

    public function setClientSecret($secret): Credential
    {
        return $this->setData(self::CLIENT_SECRET, $secret);
    }

    public function setScope($scope): Credential
    {
        return $this->setData(self::SCOPE, $scope);
    }

    public function getHash(): string
    {
        return hash('md5', implode(' ', [
            $this->getClientId(),
            $this->getClientSecret(),
            $this->getScope(),
        ]));
    }
}
