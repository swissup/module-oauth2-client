<?php
namespace Swissup\OAuth2Client\Api;

interface FlowTokenInterface
{
    public function getToken();

    public function validate($token);

    public function validateRequest(\Magento\Framework\App\RequestInterface $request);
}
