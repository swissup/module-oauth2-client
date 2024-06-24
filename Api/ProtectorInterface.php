<?php
namespace Swissup\OAuth2Client\Api;

use Magento\Framework\Model\AbstractModel;

interface ProtectorInterface
{
    /**
    * Encrypt a string
    *
    * @param string $data
    * @return string
    */
    public function encrypt(AbstractModel $object): void;

    /**
    * Decrypt a string
    *
    * @param string $data
    * @return string
    */
    public function decrypt(AbstractModel $object): void;
}
