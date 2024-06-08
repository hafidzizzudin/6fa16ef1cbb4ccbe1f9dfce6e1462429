<?php

namespace Src\Module;

use Src\Entity\SendEmailRequest;

interface EmailSender
{
    public function send(SendEmailRequest $req);
}
