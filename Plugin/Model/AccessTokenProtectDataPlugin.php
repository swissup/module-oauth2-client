<?php
declare(strict_types=1);

namespace Swissup\OAuth2Client\Plugin\Model;

use Swissup\OAuth2Client\Api\ProtectorInterface;
use Swissup\OAuth2Client\Model\AccessToken;

class AccessTokenProtectDataPlugin
{
    /**
     * @var ProtectorInterface
     */
    private ProtectorInterface $protector;

    /**
     * @param \Swissup\Email\Api\ServiceEncryptorInterface $serviceEncryptor
     */
    public function __construct(ProtectorInterface $protector)
    {
        $this->protector = $protector;
        $this->protector->setProtectedColumnNames(['access_token', 'refresh_token', 'resource_owner_id']);
    }

    public function afterBeforeSave(AccessToken $subject): void
    {
        $this->protector->encrypt($subject);
    }

    public function afterAfterSave(AccessToken $subject): void
    {
        $this->protector->decrypt($subject);
    }

    public function afterAfterLoad(AccessToken $subject): void
    {
        $this->protector->decrypt($subject);
    }
}
