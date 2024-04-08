<?php

declare(strict_types=1);

namespace App\Turbo\Stream;

use App\Turbo\Stream\Model\AbstractStream;

class NotificationStream extends AbstractStream
{
    public function __construct(string $id, private string $type, private string $message)
    {
        parent::__construct($id);
    }

    protected function getAction(): string
    {
        return 'append';
    }

    protected function getHtml(): ?string
    {
        $type = $this->type;
        $message = $this->message;

        return <<<HTML
        <meta data-controller="notification" data-notification-type-value="$type" data-notification-message-value="$message" >
HTML;
    }
}
