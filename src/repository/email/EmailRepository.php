<?php

namespace Src\Repository\Email;

use Src\Entity\SendEmailRequest;

interface EmailRepository
{
    public function save(SendEmailRequest $req);
}
