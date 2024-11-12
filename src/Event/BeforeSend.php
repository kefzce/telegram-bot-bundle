<?php

namespace Luzrain\TelegramBotBundle\Event;

use Luzrain\TelegramBotApi\Method;

readonly class BeforeSend
{
    public function __construct(public Method $method)
    {

    }
}