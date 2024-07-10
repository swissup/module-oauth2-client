<?php
namespace Swissup\OAuth2Client\Controller\Google;

use Swissup\OAuth2Client\Model\AccessTokenRepository;

class GetToken extends \Magento\Framework\App\Action\Action
{
    const SESSION_TOKEN_ID_KEY = 'swissup_oauth_client_access_token_id';
    const FLOW_STATE_KEY = 'oauth2state';

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    private $session;

    /**
     * @var AccessTokenRepository
     */
    private $accessTokenRepository;

    /**
     * @var \Magento\Framework\Url\DecoderInterface
     */
    private $urlDecoder;

    /**
     * @var \Swissup\OAuth2Client\Model\Data\BearerToken
     */
    private $bearerValidator;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Swissup\OAuth2Client\Model\AccessTokenRepository $accessTokenRepository,
        \Magento\Framework\Url\DecoderInterface $urlDecoder,
        \Swissup\OAuth2Client\Model\Data\BearerToken $bearerValidator
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->accessTokenRepository = $accessTokenRepository;

        $this->urlDecoder = $urlDecoder;
        $this->bearerValidator = $bearerValidator;
    }

    private function isLoggedIn()
    {
        $request = $this->getRequest();
        $tokenId = $request->getParam('token_id', false);
        $sessionIdKey = self::SESSION_TOKEN_ID_KEY;

        if (!empty($tokenId)) {
            $sessionValue = null;
            if ($this->bearerValidator->validateRequest($request)) {
                $sessionValue = $tokenId;
            }
            $this->session->setData($sessionIdKey, $sessionValue);
        }

        return (bool) $this->session->getData($sessionIdKey);
    }

    private function getTokenId()
    {
        $sessionIdKey = self::SESSION_TOKEN_ID_KEY;
        return (int) $this->session->getData($sessionIdKey);
    }

    private function redirectReferer()
    {
        $refererUrl = $this->session->getData('referer');
        $refererUrl = empty($refererUrl) ? $this->_redirect->getRedirectUrl() : $refererUrl;
        return $this->_redirect($refererUrl);
    }

    private function setFlowState($state)
    {
        return $this->session->setData(self::FLOW_STATE_KEY, $state);
    }

    private function resetFlowState()
    {
        return $this->setFlowState(null);
    }

    private function isValidFlowState($state): bool
    {
        return empty($state) || ($state !== $this->session->getData(self::FLOW_STATE_KEY));
    }

    /**
     * Post user question
     *
     * @inherit
     */
    public function execute()
    {
        if (!$this->isLoggedIn()) {
            return $this->redirectReferer();
        }

        $tokenId = $this->getTokenId();
        /* @var \Swissup\OAuth2Client\Model\AccessToken $accessToken */
        $accessToken = $this->accessTokenRepository->getById($tokenId);

        $request = $this->getRequest();
        $refererUrl = $request->getParam('referer');
        $refererUrl = $this->urlDecoder->decode($refererUrl);
        if (!empty($refererUrl)) {
            $this->session->setData('referer', $refererUrl);
        }

        /* @var \League\OAuth2\Client\Provider\Google $provider */
        $provider = $accessToken->getProvider();

        $errorParam = $request->getParam('error');
        $codeParam = $request->getParam('code');
        $stateParam = $request->getParam('state');

        if (!empty($errorParam)) {
            $this->messageManager->addErrorMessage(
                htmlspecialchars($errorParam, ENT_QUOTES, 'UTF-8')
            );
            return $this->redirectReferer();
        } elseif (empty($codeParam)) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl(['scope' => ['https://mail.google.com/']]);
            $this->setFlowState($provider->getState());

            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl($authUrl);

            return $resultRedirect;
        // Check given state against previously stored one to mitigate CSRF attack
        } elseif ($this->isValidFlowState($stateParam)) {
            $this->resetFlowState();
            $this->messageManager->addErrorMessage(
                __('Invalid state')
            );
            return $this->redirectReferer();
        } else {
            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $codeParam
            ]);

            $refreshToken = $token->getRefreshToken();
            if (empty($refreshToken)) {
                $authUrl = $provider->getAuthorizationUrl(['prompt' => 'consent', 'access_type' => 'offline']);
                $this->setFlowState($provider->getState());

                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setUrl($authUrl);
                return $resultRedirect;
            }
            $this->resetFlowState();

            $tokenOptions = array_merge($token->jsonSerialize(), ['refresh_token' => $refreshToken]);
            $accessToken->addData($tokenOptions);
            $this->accessTokenRepository->save($accessToken);
        }

        return $this->redirectReferer();
    }
}
