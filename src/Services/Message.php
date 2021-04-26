<?php

namespace Thotam\ThotamGmail\Services;

use Google_Client;
use Google_Service_Gmail;
use Thotam\ThotamGmail\Services\Traits\ClientTraits;

class Message
{
    use ClientTraits;

    public $service;

	public $preload = false;

	public $client;

	public $emailAddress;

	/**
	 * Optional parameter for getting single and multiple emails
	 *
	 * @var array
	 */
	protected $params = [];

	public function __construct($refreshToken = NULL, $clientSecret = NULL, $clientId = NULL)
	{
		$this->client = new Google_Client();
        $this->setClientSecret($clientSecret);
        $this->setClientId($clientId);
        $this->refreshToken($refreshToken);
		$this->service = new Google_Service_Gmail($this->client);
        $me = $this->service->users->getProfile("me");
        if (property_exists($me, 'emailAddress')) {
            $this->emailAddress = $me->emailAddress;
        } else {
            throw new \Exception('Cannot get emailAddress, please check config');
        }
	}
}
