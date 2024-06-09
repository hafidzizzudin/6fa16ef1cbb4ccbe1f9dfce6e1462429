<?php

namespace Src\Entity;

class Email
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    private ?int $id;
    private string $status = '';
    private string $note = '';

    public function __construct(
        private string $userID,
        private bool $isHtml,
        private string $emailTo,
        private string $body,
        private string $subject,
    ) {
    }

    public function setID(int $id)
    {
        $this->id = $id;
    }

    public function setNote(string $note)
    {
        $this->note = $note;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function getID(): int
    {
        return $this->id;
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

    public function toArrayInsert(): array
    {
        return [
            'user_id' => $this->userID,
            'is_html' => (int)$this->isHtml,
            'email_to' => $this->emailTo,
            'body' => $this->body,
            'subject' => $this->subject,
        ];
    }

    public function toArrayQueue(): array
    {
        $result = $this->toArrayInsert();
        $result['id'] = $this->id;
        return $result;
    }

    public static function fromRequest(array $data): Email
    {
        return new Email(
            $data['user_id'],
            $data['is_html'],
            $data['email_to'],
            $data['body'],
            $data['subject'],
        );
    }

    public static function fromArray(array $data): Email
    {
        $email = new Email(
            $data['user_id'],
            $data['is_html'],
            $data['email_to'],
            $data['body'],
            $data['subject'],
        );

        $email->id = $data['id'];

        return $email;
    }

    public function toUpdateResultArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'note' => $this->note,
        ];
    }
}
