<?php
namespace Swissup\OAuth2Client\Api;

interface EncryptorInterface
{
    /**
    * Encrypt a string
    *
    * @param string $data
    * @return string
    */
    public function encrypt($data);

    /**
    * Decrypt a string
    *
    * @param string $data
    * @return string
    */
    public function decrypt($data);
}
