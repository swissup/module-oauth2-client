<?php
namespace Swissup\OAuth2Client\Model;

use Swissup\OAuth2Client\Api\Data\AccessTokenInterface;
use Magento\Framework\Model\AbstractModel;

class AccessToken extends AbstractModel implements AccessTokenInterface
{
    /**
     * @var \Magento\Framework\Url
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    private $urlEncoder;

    /**
     * @var \Swissup\OAuth2Client\Model\Data\BearerToken
     */
    private $bearerToken;

    /**
     * @var \Swissup\OAuth2Client\Model\CredentialFactory
     */
    private $credentialFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Url $urlBuilder,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Swissup\OAuth2Client\Model\Data\BearerToken $bearerToken,
        \Swissup\OAuth2Client\Model\CredentialFactory $credentialFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->urlBuilder = $urlBuilder;
        $this->urlEncoder = $urlEncoder;
        $this->bearerToken = $bearerToken;
        $this->credentialFactory = $credentialFactory;
    }

    protected function _construct()
    {
        $this->_init(\Swissup\OAuth2Client\Model\ResourceModel\AccessToken::class);
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getProviderType()
    {
        return $this->getData(self::PROVIDER_TYPE);
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

    public function setProviderType($providerType)
    {
        return $this->setData(self::PROVIDER_TYPE, $providerType);
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

    public function isInitialized()
    {
        $token = $this->getAccessToken();
        return !empty($token);
    }

    public function getCallbackUrl()
    {
        $urlBuilder = $this->urlBuilder;
        $refererUrl = $urlBuilder->getCurrentUrl();
        $refererUrl = $this->urlEncoder->encode($refererUrl);
        $callbackUrl = $urlBuilder->getUrl(
            'swissup_oauth2client/google/getToken',
            [
                '_nosid' => true,
                '_query' => [
                    'token_id' => $this->getId(),
                    'referer' => $refererUrl,
                    $this->bearerToken->getParamName() => $this->bearerToken->getToken(),
                ]
            ]
        );

        return $callbackUrl;
    }

    /**
     * @see EmailStorageOAuth2TokenPlugin::afterAfterLoad
     * @return Credential
     */
    public function getCredential()
    {
        $credential = $this->credentialFactory->create();
        return $credential->getByHash($this->getCredentialHash());
    }

    public function getProvider()
    {
        $providerType = (int) $this->getProviderType();
        $provider = null;
        if ($providerType === 0) {
            $credential = $this->getCredential();
            $redirectUri = $this->urlBuilder->getUrl('swissup_oauth2client/google/getToken');
            $scopes = empty($scope) ? ['https://mail.google.com/'] : explode(' ', $credential->getScope());
            /* @var \League\OAuth2\Client\Provider\Google $provider */
            $provider = new \League\OAuth2\Client\Provider\Google([
                'clientId'     => $credential->getClientId(),
                'clientSecret' => $credential->getClientSecret(),
                'redirectUri'  => $redirectUri,
    //            'hostedDomain' => 'example.com', // optional; used to restrict access to users on your G Suite/Google Apps for Business accounts
                'scopes' => $scopes,
                'accessType' => 'offline'
            ]);
        }

        return $provider;
    }

    public function runRefreshToken()
    {
        $providerType = (int) $this->getProviderType();
        if ($providerType !== 0) {
            return $this;
        }
        $tokenOptions = $this->toArray([
            self::ACCESS_TOKEN,
            self::REFRESH_TOKEN,
            self::EXPIRES,
            self::RESOURCE_OWNER_ID,
        ]);
        if (empty($tokenOptions[self::ACCESS_TOKEN])) {
            return $this;
        }
        $storedAccessToken = new \League\OAuth2\Client\Token\AccessToken($tokenOptions);
        $refreshToken = $storedAccessToken->getRefreshToken();
        if (!$storedAccessToken->hasExpired() || empty($refreshToken)) {
            return $this;
        }
        /* @var \League\OAuth2\Client\Provider\Google $provider */
        $provider = $this->getProvider();
        $refreshedAccessToken = $provider->getAccessToken('refresh_token', [
            'refresh_token' => $refreshToken
        ]);
        $tokenOptions = array_merge($refreshedAccessToken->jsonSerialize(), ['refresh_token' => $refreshToken]);
        return $this->addData($tokenOptions);
    }
}
