<?php

namespace Src\Repository\Email;

use Src\Entity\Email;

interface EmailRepository
{
    public function save(Email $req): int;
    public function updateResult(Email $req);
}
