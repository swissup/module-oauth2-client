<?php

namespace Swissup\OAuth2Client\Model\Data;

use Magento\Framework\Encryption\Helper\Security;

class FlowToken implements \Swissup\OAuth2Client\Api\FlowTokenInterface
{
    const CACHE_ID = 'swissup_oauth2_client_flow_1h_token';
    const CACHE_TAG = 'swissup_oauth2_client';
    const LIFETIME = 3600; // 1 hour

    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    /**
     * @var \Magento\Framework\Cache\FrontendInterface
     */
    private $cache;

    /**
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontend
     */
    public function __construct(
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontend
    ) {
        $this->mathRandom = $mathRandom;
        $this->cache = $cacheFrontend->get(\Magento\Framework\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID);
    }

    /**
     * Retrieve State Token
     *
     * @return string A 16 bit unique key
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getToken()
    {
        if (!$this->isPresent()) {
            $this->set($this->mathRandom->getRandomString(16));
        }
        return $this->cache->load(self::CACHE_ID);
    }

    /**
     * Determine if the token is present in the 'session'
     *
     * @return bool
     */
    private function isPresent()
    {
        return (bool) $this->cache->test(self::CACHE_ID);
    }

    /**
     * Save the value of the token
     *
     * @param string $value
     * @return void
     */
    private function set($value)
    {
        $this->cache->save((string) $value, self::CACHE_ID, [self::CACHE_TAG], self::LIFETIME);
    }

    /**
     * Validate
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function validate($token)
    {
        return $token && Security::compareStrings($token, $this->getToken());
    }

    /**
     * Validate request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function validateRequest(\Magento\Framework\App\RequestInterface $request)
    {
        $token = $request->getParam('token', null);
        return $this->validate($token);
    }
}
