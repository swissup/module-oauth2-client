<?php
namespace Swissup\OAuth2Client\Model\Data;

use Swissup\OAuth2Client\Api\EncryptorInterface;
use Swissup\OAuth2Client\Api\ProtectorInterface;
use Magento\Framework\Model\AbstractModel;

class Protector implements ProtectorInterface
{
    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    private $protectedColumnNames = [];

    /**
     * @param EncryptorInterface $encryptor
     */
    public function __construct(EncryptorInterface $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    /**
     * @return string[]
     */
    private function getProtectedColumnNames()
    {
        return $this->protectedColumnNames;
    }

    /**
     * @param array $protectedColumnNames
     * @return $this
     */
    public function setProtectedColumnNames(array $protectedColumnNames)
    {
        $this->protectedColumnNames = $protectedColumnNames;
        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return void
     */
    public function encrypt(AbstractModel $object): void
    {
        foreach ($this->getProtectedColumnNames() as $columnName) {
            $value = $object->getData($columnName);
            if ($value) {
                $object->setData($columnName, $this->encryptor->encrypt($value));
            }
        }
    }

    /**
     * @param AbstractModel $object
     * @return void
     */
    public function decrypt(AbstractModel $object): void
    {
        foreach ($this->getProtectedColumnNames() as $columnName) {
            $value = $object->getData($columnName);
            if ($value) {
                try {
                    $value = $this->encryptor->decrypt($value);
                    $object->setData($columnName, $value);
                } catch (\Exception $e) {
                    // value is not encrypted or something wrong with encrypted data
                }
            }
        }
    }
}
