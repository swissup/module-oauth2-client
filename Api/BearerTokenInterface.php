<?php
namespace Swissup\OAuth2Client\Api;

interface BearerTokenInterface
{
    public function getToken();

    public function validate($token);

    public function getParamName():string;

    public function validateRequest(\Magento\Framework\App\RequestInterface $request);
}
