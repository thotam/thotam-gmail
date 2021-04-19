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
            $this->client->refreshToken(env('DEFAULT_GOOGLE_REFRESH_TOKEN'));
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
            $this->client->setClientId(env('DEFAULT_GOOGLE_CLIENT_ID'));
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
            $this->client->setClientSecret(env('DEFAULT_GOOGLE_CLIENT_SECRET'));
        } else {
            $this->client->setClientSecret($clientSecret);
        }
    }
}
