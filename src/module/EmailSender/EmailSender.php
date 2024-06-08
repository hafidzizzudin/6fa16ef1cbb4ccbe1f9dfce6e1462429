<?php

namespace Src\Module\EmailSender;

use Src\Entity\Email;

interface EmailSender
{
    public function send(Email $req);
    public function sendEmail(Email $req);
    public function resetConnection();
}
