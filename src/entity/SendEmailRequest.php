<?php

namespace Src\Entity;

class SendEmailRequest
{
    public function __construct(
        private string $userID,
        private bool $isHtml,
        private string $emailTo,
        private string $body,
        private string $subject = 'Send Email'
    ) {
    }

    public function getUserID(): string
    {
        return $this->userID;
    }

    public function getIsHtml(): bool
    {
        return $this->isHtml;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getEmailTo(): string
    {
        return $this->emailTo;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userID,
            'is_html' => (int)$this->isHtml,
            'email_to' => $this->emailTo,
            'body' => $this->body,
            'subject' => $this->subject,
        ];
    }
}
