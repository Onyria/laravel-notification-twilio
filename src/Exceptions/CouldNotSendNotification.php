<?php

namespace NotificationChannels\Twilio\Exceptions;

class CouldNotSendNotification extends \Exception
{
    public static function twilioRespondedWithAnError($response)
    {
        return new static($response);
    }
}
