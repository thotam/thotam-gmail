<?php

namespace Thotam\ThotamGmail\Traits;

use Thotam\ThotamGmail\Models\HasMail;

trait HasMailTraits {

    /**
     * Get all of the model's gmails.
     */
    public function gmails()
    {
        return $this->morphMany(HasMail::class, 'gmail');
    }

    /**
     * addMail
     *
     * @param  mixed $mail
     * @param  mixed $tag
     * @return void
     */
    public function addMail(HasMail $mail, String $tag = null)
    {
        if (!!$tag) {
            $mail->update(["tag" => $tag]);
        }

        $this->gmails()->save($mail);
    }

    /**
     * addMails
     *
     * @param  mixed $mails
     * @param  mixed $tag
     * @return void
     */
    public function addMails($mails, String $tag = null)
    {
        foreach ($mails as $mail) {
            $this->addMail($mail, $tag);
        }
    }

    /**
     * removeMail
     *
     * @param  mixed $lib
     * @return void
     */
    public function removeMail(HasMail $mail)
    {
        $mail->gmail()->dissociate()->save();
    }

    /**
     * removeMails
     *
     * @param  mixed $mails
     * @param  mixed $tag
     * @return void
     */
    public function removeMails($mails, String $tag = null)
    {
        foreach ($mails as $mail) {
            $this->removeMail($mail);
        }
    }

    /**
     * getMail
     *
     * @param  mixed $tag
     * @return void
     */
    public function getMail(String $tag = null)
    {
        $mail = $this->gmails()->latest();

        if (!!$tag) {
            $mail->where("tag", $tag);
        }

        return $mail->first();
    }

    /**
     * getMails
     *
     * @param  mixed $tag
     * @return void
     */
    public function getMails(String $tag = null)
    {
        $mail = $this->gmails();

        if (!!$tag) {
            $mail->where("tag", $tag);
        }

        return $mail->get();
    }

}
