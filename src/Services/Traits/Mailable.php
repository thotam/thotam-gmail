<?php

namespace Thotam\ThotamGmail\Services\Traits;

use Swift_Attachment;
use Google_Service_Gmail;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Google_Service_Gmail_Message;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Thotam\ThotamGmail\Services\Message\Mail;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

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
        $this->swiftMessage = new Email();
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
            if (is_null($name)) {
                $name = explode('@', $to)[0];
            }

            $this->to[] = new Address($to, $name);
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
                $this->to[] = new Address($email, $email_name);
            }
        } else {
            if (is_null($name)) {
                $name = explode('@', $to)[0];
            }
            $this->to[] = new Address($to, $name);
        }

        return $this;
    }

    public function setFrom($name = null)
    {
        $from = $this->getUser();
        if (is_null($name)) {
            $name = explode('@', $from)[0];
        }
        $this->from = new Address($from, $name);

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
            if (is_null($name)) {
                $name = explode('@', $cc)[0];
            }
            $this->cc[] = new Address($cc, $name);
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
                $this->cc[] = new Address($email, $email_name);
            }
        } else {
            if (is_null($name)) {
                $name = explode('@', $cc)[0];
            }
            $this->cc[] = new Address($cc, $name);
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
            if (is_null($name)) {
                $name = explode('@', $bcc)[0];
            }
            $this->bcc[] = new Address($bcc, $name);
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
                $this->bcc[] = new Address($email, $email_name);
            }
        } else {
            if (is_null($name)) {
                $name = explode('@', $bcc)[0];
            }
            $this->bcc[] = new Address($bcc, $name);
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
    public function reply(string $threadId, bool $replyAll = true)
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

        if ($replyAll) {
            $this->setReplyCc();
            $this->setReplyBcc();
        }

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
                $References .= " " . $Message_ID;
            } else {
                $References = $Message_ID;
            }

            if (!!!$In_Reply_To) {
                $In_Reply_To = $Message_ID;
            }

            $this->setHeader('In-Reply-To', $In_Reply_To);
            $this->setHeader('References', $References);
            //$this->setHeader('Message-ID', $Message_ID);
            //dd($Message_ID);

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

                if (!!$replyTo['email'] && Str::contains($replyTo['email'], '@') && $replyTo['email'] != $this->getUser() && $replyTo['email'] != "mailer-daemon@googlemail.com") {
                    $this->to[] = new Address($replyTo['email'], $replyTo['name']);
                }
            }
        }
    }

    private function setReplyFrom()
    {
        if (!$this->from) {
            $this->from = $this->getUser();
            if (!$this->from) {
                throw new \Exception('Reply from is not defined');
            }
        }
    }

    private function setReplyCc()
    {
        $temp_cc = [];
        foreach ($this->messages as $message) {
            $this->setMessage($message);

            $tos = $this->getHeader('To');
            $ccs = $this->getHeader('CC');

            $toa = explode(", ", $tos);
            $cca = explode(", ", $ccs);

            $cc = array_merge($toa, $cca);
            foreach ($cc as $c) {
                preg_match('/<(.*)>/', $c, $matches);

                $name = preg_replace('/ <(.*)>/', '', $c);
                $name = preg_replace('/"|\\\\/', '', $name);

                if (isset($matches[1])) {
                    $temp_cc[$matches[1]] = $name;
                }
            }
        }

        $temp_cc = array_unique($temp_cc);
        $temp_cc = Arr::where($temp_cc, function ($value, $key) {
            return ($key != $this->getUser()) && ($key != "mailer-daemon@googlemail.com");
        });

        foreach ($temp_cc as $cc_email => $cc_name) {
            $this->cc[] = new Address($cc_email, $cc_name);
        }
    }

    private function setReplyBcc()
    {
        $temp_bcc = [];
        foreach ($this->messages as $message) {
            $this->setMessage($message);
            $bccs = $this->getHeader('BCC');
            $bcca = explode(", ", $bccs);

            foreach ($bcca as $bcc) {
                preg_match('/<(.*)>/', $bcc, $matches);

                $name = preg_replace('/ <(.*)>/', '', $bcc);
                $name = preg_replace('/"|\\\\/', '', $name);

                if (isset($matches[1])) {
                    $temp_bcc[$matches[1]] = $name;
                }
            }
        }
        $temp_bcc = array_unique($temp_bcc);

        $temp_bcc = Arr::where($temp_bcc, function ($value, $key) {
            return ($key != $this->getUser()) && ($key != "mailer-daemon@googlemail.com");
        });

        foreach ($temp_bcc as $bcc_email => $bcc_name) {
            $this->bcc[] = new Address($bcc_email, $bcc_name);
        }
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

        if (is_array($this->to) && count($this->to) == 0) {
            $this->to = NULL;
        }
        if (is_array($this->cc) && count($this->cc) == 0) {
            $this->cc = NULL;
        }
        if (is_array($this->bcc) && count($this->bcc) == 0) {
            $this->bcc = NULL;
        }

        $this->swiftMessage
            ->subject($this->subject)
            ->from($this->from)
            ->html($this->message, 'text/html')
            ->priority($this->priority);

        if ((bool)$this->to) {
            $this->swiftMessage->to(...$this->to);
        }
        if ((bool)$this->cc) {
            $this->swiftMessage->cc(...$this->cc);
        }
        if ((bool)$this->bcc) {
            $this->swiftMessage->bcc(...$this->bcc);
        }

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
