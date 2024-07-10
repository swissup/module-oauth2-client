<?php
namespace Swissup\OAuth2Client\Model;

use Swissup\OAuth2Client\Api\Data\CredentialInterface;
use Magento\Framework\DataObject;

class Credential extends DataObject implements CredentialInterface
{
    const CACHE_ID_PREFIX = 'credential';
    const CACHE_TAG = 'swissup_oauth2_client';
    const LIFETIME = 3600;// 1 hour

    /**
     * @var \Magento\Framework\Cache\FrontendInterface
     */
    private $cacheFrontend;

     /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    public function __construct(
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        array $data = []
    ) {
        $this->cacheFrontend = $cacheFrontendPool->get(\Magento\Framework\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID);
        $this->serializer = $serializer;
        $this->encryptor = $encryptor;
        parent::__construct($data);
    }

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

    private function getCacheId($hash)
    {
        return implode('_', [
            self::CACHE_TAG,
            self::CACHE_ID_PREFIX,
            $hash
        ]);
    }

    public function save()
    {
        $data = $this->toArray([
            self::CLIENT_ID,
            self::CLIENT_SECRET,
            self::SCOPE,
        ]);
        $data = $this->serializer->serialize($data);
        $data = $this->encryptor->encrypt($data);
        $id = $this->getCacheId($this->getHash());
        return $this->cacheFrontend->save($data, $id, [self::CACHE_TAG], self::LIFETIME);
    }

    public function isExpired()
    {
        $hash = $this->getHash();
        $id = $this->getCacheId($hash);
        return $this->cacheFrontend->test($id) === false;
    }

    public function getByHash($hash)
    {
        $id = $this->getCacheId($hash);
        $isSaved = (bool) $this->cacheFrontend->test($id);
        if ($isSaved) {
            $data = $this->cacheFrontend->load($id);
            if ($data) {
                $data = $this->encryptor->decrypt($data);
                $data = $this->serializer->unserialize($data);
                $this->addData($data);
            }
        }

        return $this;
    }
}
