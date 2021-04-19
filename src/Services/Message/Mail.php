<?php

namespace Thotam\ThotamGmail\Services\Message;

use Google_Client;
use Google_Service_Gmail;
use Thotam\ThotamGmail\Services\Message;
use Thotam\ThotamGmail\Services\Traits\Replyable;

class Mail extends Message
{
    use Replyable {
        Replyable::__construct as private __rConstruct;
    }

	/**
	 * @var \Google_Service_Gmail_MessagePart
	 */
	public $payload;

    /**
     * __construct
     *
     * @param  mixed $refreshToken
     * @param  mixed $clientSecret
     * @param  mixed $clientId
     * @return void
     */
    public function __construct($refreshToken = NULL, $clientSecret = NULL, $clientId = NULL)
	{
        $this->__rConstruct();
        parent::__construct($refreshToken, $clientSecret, $clientId);
    }

	/**
	 * Sets data from mail
	 *
	 * @param \Google_Service_Gmail_Message $message
	 */
	protected function setMessage(\Google_Service_Gmail_Message $message)
	{
		$this->id = $message->getId();
		$this->internalDate = $message->getInternalDate();
		$this->labels = $message->getLabelIds();
		$this->size = $message->getSizeEstimate();
		$this->threadId = $message->getThreadId();
		$this->payload = $message->getPayload();
		if ($this->payload) {
			$this->parts = collect($this->payload->getParts());
		}
	}

	/**
	 * Returns all the headers of the email
	 *
	 * @return Collection
	 */
	public function getHeaders()
	{
		return $this->buildHeaders($this->payload->getHeaders());
	}

	/**
	 * Gets all the headers from an email and returns a collections
	 *
	 * @param $emailHeaders
	 * @return Collection
	 */
	private function buildHeaders($emailHeaders)
	{
		$headers = [];

		foreach ($emailHeaders as $header) {
			/** @var \Google_Service_Gmail_MessagePartHeader $header */

			$head = new \stdClass();

			$head->key = $header->getName();
			$head->value = $header->getValue();

			$headers[] = $head;
		}

		return collect($headers);
	}
}
