<?php
namespace Swissup\OAuth2Client\Api\Data;

interface AccessTokenInterface
{
    const ID = 'id';
    const PROVIDER_TYPE = 'provider_type';
    const CREDENTIAL_HASH = 'credential_hash';
    const ACCESS_TOKEN = 'access_token';
    const REFRESH_TOKEN = 'refresh_token';
    const EXPIRES = 'expires';
    const RESOURCE_OWNER_ID = 'resource_owner_id';

    public function getId();
    public function getProviderType();
    public function getCredentialHash();
    public function getAccessToken();
    public function getRefreshToken();
    public function getExpires();
    public function getResourceOwnerId();

    public function setId($id);
    public function setProviderType($providerType);
    public function setCredentialHash($credentialHash);
    public function setAccessToken($accessToken);
    public function setRefreshToken($refreshToken);
    public function setExpires($expires);
    public function setResourceOwnerId($resourceOwnerId);
}
