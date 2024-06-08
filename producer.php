<?php

use Src\Entity\Email;

require 'bootstrap.php';

for ($i = 0; $i < 1; $i++) {
    $emailService->sendEmail(new Email("user_id_" . ($i + 1), true, 'hafidz.izzudin' . ($i + 1) . '@gmail.com', '<br>Nice adasdasd<br>', "payslip " . ($i + 1)));
}
