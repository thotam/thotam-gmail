<?php
namespace Thotam\ThotamGmail\Services\Traits;

trait ClientTraits
{
    /**
     * For backwards compatibility
     * alias for fetchAccessTokenWithRefreshToken
     *
     * @param string $refreshToken
     * @return array access token
     */
    public function refreshToken($refreshToken = NULL)
    {
        if ($refreshToken == null) {
            $this->client->refreshToken(config('thotam-gmail.mail.refreshToken'));
        } else {
            $this->client->refreshToken($refreshToken);
        }
    }

    /**
     * Set the OAuth 2.0 Client ID.
     * @param string $clientId
     */
    public function setClientId($clientId = NULL)
    {
        if ($clientId == null) {
            $this->client->setClientId(config('thotam-gmail.mail.clientId'));
        } else {
            $this->client->setClientId($clientId);
        }
    }

    /**
     * Set the OAuth 2.0 Client Secret.
     * @param string $clientSecret
     */
    public function setClientSecret($clientSecret = NULL)
    {
        if ($clientSecret == null) {
            $this->client->setClientSecret(config('thotam-gmail.mail.clientSecret'));
        } else {
            $this->client->setClientSecret($clientSecret);
        }
    }
}
