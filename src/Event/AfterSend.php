<?php

namespace Luzrain\TelegramBotBundle\Event;

use Luzrain\TelegramBotApi\Type;

readonly class AfterSend
{
    public function __construct(public Type $type)
    {

    }
}