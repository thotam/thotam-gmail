<?php

namespace Thotam\ThotamGmail\Services\Traits;

use Swift_Message;
use Swift_Attachment;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Thotam\ThotamGmail\Services\Message\Mail;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @property Google_Service_Gmail $service
 */
trait Mailable
{
	use HasHeaders;

	private $swiftMessage;

	/**
	 * Gmail optional parameters
	 *
	 * @var array
	 */
	private $parameters = [];

	/**
	 * All messages
	 *
	 * @var string
	 */
	private $messages;

	/**
	 * Text or html message to send
	 *
	 * @var string
	 */
	private $message;

	/**
	 * Subject of the email
	 *
	 * @var string
	 */
	private $subject;

	/**
	 * Sender's email
	 *
	 * @var string
	 */
	private $from;

	/**
	 * Email of the recipient
	 *
	 * @var array
	 */
	private $to = [];

	/**
	 * Single email or array of email for a carbon copy
	 *
	 * @var array
	 */
	private $cc = [];

	/**
	 * Single email or array of email for a blind carbon copy
	 *
	 * @var array
	 */
	private $bcc = [];

	/**
	 * List of attachments
	 *
	 * @var array
	 */
	private $attachments = [];

	private $priority = 2;

	public function __construct()
	{
		$this->swiftMessage = new Swift_Message();
	}

	/**
	 * Receives the recipient's
	 * If multiple recipients will receive the message an array should be used.
	 * Example: array('receiver@domain.org', 'other@domain.org' => 'A name')
	 *
	 * If $name is passed and the first parameter is a string, this name will be
	 * associated with the address.
	 *
	 * @param  string|array  $to
	 *
	 * @param  string|null  $name
	 *
	 * @return Mailable
	 */
	public function setTo($to, $name = null)
	{
        if (is_array($to)) {
            $this->to = $to;
        } else {
            $this->to = [];
            if (is_null($name)) {
                $name = explode('@', $to)[0];
            }
            $this->to[$to] = $name;
        }

		return $this;
	}

	/**
	 * Receives the recipient's
	 * If multiple recipients will receive the message an array should be used.
	 * Example: array('receiver@domain.org', 'other@domain.org' => 'A name')
	 *
	 * If $name is passed and the first parameter is a string, this name will be
	 * associated with the address.
	 *
	 * @param  string|array  $to
	 *
	 * @param  string|null  $name
	 *
	 * @return Mailable
	 */
	public function addTo($to, $name = null)
	{
        if (is_array($to)) {
            foreach ($to as $email => $email_name) {
                $this->to[$email] = $email_name;
            }
        } else {
            if (is_null($name)) {
                $name = explode('@', $to)[0];
            }
            $this->to[$to] = $name;
        }

		return $this;
	}

	public function setFrom($name = null)
	{
        $from = $this->getUser();
        $this->from = [];
        if (is_null($name)) {
            $name = explode('@', $from)[0];
        }
        $this->from[$from] = $name;

		return $this;
	}

    /**
     * setCc
     *
     * @param  array|string $cc
     * @param  string|null $name
     * @return Mailable
     */
    public function setCc($cc, $name = null)
    {
        if (is_array($cc)) {
            $this->cc = $cc;
        } else {
            $this->cc = [];
            if (is_null($name)) {
                $name = explode('@', $cc)[0];
            }
            $this->cc[$cc] = $name;
        }

		return $this;
    }

    /**
     * addCc
     *
     * @param  array|string $cc
     * @param  string|null $name
     * @return Mailable
     */
    public function addCc($cc, $name = null)
    {
        if (is_array($cc)) {
            foreach ($cc as $email => $email_name) {
                $this->cc[$email] = $email_name;
            }
        } else {
            if (is_null($name)) {
                $name = explode('@', $cc)[0];
            }
            $this->cc[$cc] = $name;
        }

		return $this;
    }

	/**
	 * @param  array|string  $bcc
	 *
	 * @param  string|null  $name
	 *
	 * @return Mailable
	 */
	public function setBcc($bcc, $name = null)
	{
        if (is_array($bcc)) {
            $this->bcc = $bcc;
        } else {
            $this->bcc = [];
            if (is_null($name)) {
                $name = explode('@', $bcc)[0];
            }
            $this->bcc[$bcc] = $name;
        }

		return $this;
	}

	/**
	 * @param  array|string  $bcc
	 *
	 * @param  string|null  $name
	 *
	 * @return Mailable
	 */
	public function addBcc($bcc, $name = null)
	{
        if (is_array($bcc)) {
            foreach ($bcc as $email => $email_name) {
                $this->bcc[$email] = $email_name;
            }
        } else {
            if (is_null($name)) {
                $name = explode('@', $bcc)[0];
            }
            $this->bcc[$bcc] = $name;
        }

		return $this;
	}

	/**
	 * @param  string  $subject
	 *
	 * @return Mailable
	 */
	public function subject($subject)
	{
		$this->subject = $subject;

		return $this;
	}

	/**
	 * @param  string  $view
	 * @param  array  $data
	 * @param  array  $mergeData
	 *
	 * @return Mailable
	 * @throws \Throwable
	 */
	public function view($view, $data = [], $mergeData = [])
	{
		$this->message = view($view, $data, $mergeData)->render();

		return $this;
	}

	/**
	 * @param  string  $message
	 *
	 * @return Mailable
	 */
	public function message($message)
	{
		$this->message = $message;

		return $this;
	}

	/**
	 * Attaches new file to the email from the Storage folder
	 *
	 * @param  array  $files  comma separated of files
	 *
	 * @return Mailable
	 * @throws \Exception
	 */
	public function attach(...$files)
	{

		foreach ($files as $file) {

			if (!file_exists($file)) {
				throw new FileNotFoundException($file);
			}

			array_push($this->attachments, $file);
		}

		return $this;
	}

	/**
	 * The value is an integer where 1 is the highest priority and 5 is the lowest.
	 *
	 * @param  int  $priority
	 *
	 * @return Mailable
	 */
	public function priority($priority)
	{
		$this->priority = $priority;

		return $this;
	}

	/**
	 * @param  array  $parameters
	 *
	 * @return Mailable
	 */
	public function optionalParameters(array $parameters)
	{
		$this->parameters = $parameters;

		return $this;
	}

	/**
	 * Reply to a specific email
	 *
	 * @return Mail
	 * @throws \Exception
	 */
	public function reply(string $threadId)
	{
        $users_threads = $this->getThread($threadId);
        $this->messages = $users_threads->getMessages();

        $this->setMessage($this->messages[0]);
		if (!$this->getThreadId()) {
			throw new \Exception('This is a new email. Use send().');
		}
		$this->setReplySubject();

        $this->setMessage(end($this->messages));
        $this->setReplyThread();
		$this->setReplyTo();
		$this->setReplyFrom();

		$this->setReplyCc();
		$this->setReplyBcc();
		$body = $this->getMessageBody();
		$body->setThreadId($this->getThreadId());
		$this->setMessage($this->service->users_messages->send('me', $body, $this->parameters));

		return $this;
	}

	public abstract function getId();

	private function setReplyThread()
	{
		$threadId = $this->getThreadId();
		if ($threadId) {
            $References = $this->getHeader('References');
            $Message_ID = $this->getHeader('Message-ID');
            $In_Reply_To = $this->getHeader('In-Reply-To');

            if (!!$References) {
                $References .= " ".$Message_ID;
            } else {
                $References = $Message_ID;
            }

            if (!!!$In_Reply_To) {
                $In_Reply_To = $Message_ID;
            }

			$this->setHeader('In-Reply-To', $In_Reply_To);
			$this->setHeader('References', $References);
			$this->setHeader('Message-ID', $Message_ID);
		}
	}

	public abstract function getThreadId();

	/**
	 * Add a header to the email
	 *
	 * @param  string  $header
	 * @param  string  $value
	 */
	public function setHeader($header, $value)
	{
		$headers = $this->swiftMessage->getHeaders();

		$headers->addTextHeader($header, $value);

	}


	private function setReplySubject()
	{
		if (!$this->subject) {
			$this->subject = $this->getSubject();
		}
	}

	private function setReplyTo()
	{
		if (!$this->to) {
            if ($this->getFromEmail() == $this->getUser()) {
                foreach ($this->getTo() as $mail) {
                    if (!!$mail["email"]) {
                        $this->addTo($mail["email"], $mail["name"]);
                    }
                }
            } else {
                $replyTo = $this->getReplyTo();

                if (!!$replyTo['email'] && Str::contains($replyTo['email'], '@')) {
                    $this->to[$replyTo['email']] = $replyTo['name'];
                }

                $this->to = Arr::where($this->to, function ($value, $key) {
                    return ($key != $this->getUser()) && ($key != "mailer-daemon@googlemail.com");
                });
            }
		}
	}

	private function setReplyFrom()
	{
		if (!$this->from) {
			$this->from = $this->getUser();
			if(!$this->from) {
				throw new \Exception('Reply from is not defined');
			}
		}
	}

	private function setReplyCc()
	{
        foreach ($this->messages as $message) {
            $this->setMessage($message);

            $tos = $this->getHeader('To');
            $ccs = $this->getHeader('CC');

            $toa = explode(", ",$tos);
            $cca = explode(", ",$ccs);

            $cc = array_merge($toa, $cca);
            foreach ($cc as $c) {
                preg_match('/<(.*)>/', $c, $matches);

                $name = preg_replace('/ <(.*)>/', '', $c);
                $name = preg_replace('/"|\\\\/', '', $name);

                if (isset($matches[1])) {
                    $this->cc[$matches[1]] = $name;
                }
            }
        }
        $this->cc = array_unique($this->cc);
        $this->cc = Arr::where($this->cc, function ($value, $key) {
            return ($key != $this->getUser()) && ($key != "mailer-daemon@googlemail.com");
        });
	}

	private function setReplyBcc()
	{
        foreach ($this->messages as $message) {
            $this->setMessage($message);
            $bccs = $this->getHeader('BCC');
            $bcca = explode(", ",$bccs);

            foreach ($bcca as $bcc) {
                preg_match('/<(.*)>/', $bcc, $matches);

                $name = preg_replace('/ <(.*)>/', '', $bcc);
                $name = preg_replace('/"|\\\\/', '', $name);

                if (isset($matches[1])) {
                    $this->bcc[$matches[1]] = $name;
                }
            }
        }
        $this->bcc = array_unique($this->bcc);

        $this->bcc = Arr::where($this->bcc, function ($value, $key) {
            return ($key != $this->getUser()) && ($key != "mailer-daemon@googlemail.com");
        });
	}

	public abstract function getSubject();

	public abstract function getTo();

	public abstract function getReplyTo();

	public abstract function getUser();

	/**
	 * @return Google_Service_Gmail_Message
	 */
	private function getMessageBody()
	{
		$body = new Google_Service_Gmail_Message();

		$this->swiftMessage
			->setSubject($this->subject)
			->setFrom($this->from)
			->setTo($this->to)
			->setCc($this->cc)
			->setBcc($this->bcc)
			->setBody($this->message, 'text/html')
			->setPriority($this->priority);

		foreach ($this->attachments as $file) {
			$this->swiftMessage
				->attach(Swift_Attachment::fromPath($file));
		}

		$body->setRaw($this->base64_encode($this->swiftMessage->toString()));

		return $body;
	}

	private function base64_encode($data)
	{
		return rtrim(strtr(base64_encode($data), ['+' => '-', '/' => '_']), '=');
	}

	/**
	 * Sends a new email
	 *
	 * @return self|Mail
	 */
	public function send()
	{
		$body = $this->getMessageBody();

		$this->setMessage($this->service->users_messages->send('me', $body, $this->parameters));

		return $this;
	}

	protected abstract function setMessage(\Google_Service_Gmail_Message $message);
    protected abstract function getMessage(string $messageId);
}
