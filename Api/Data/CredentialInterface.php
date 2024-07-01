<?php
namespace Swissup\OAuth2Client\Api\Data;

interface CredentialInterface
{
    const CLIENT_ID = 'client_id';
    const CLIENT_SECRET = 'client_secret';
    const SCOPE = 'scope';

    public function getClientId();
    public function getClientSecret();
    public function getScope();

    public function setClientId($id);
    public function setClientSecret($secret);
    public function setScope($scope);
}
