<?php

namespace NotificationChannels\Twilio;

use NotificationChannels\Twilio\Exceptions\CouldNotSendNotification;
use NotificationChannels\Twilio\Events\MessageWasSent;
use NotificationChannels\Twilio\Events\SendingMessage;
use Illuminate\Notifications\Notification;
use Services_Twilio as Twilio;
use Services_Twilio_RestException as TwilioRestException;

class Channel
{
    /**
     * @var Twilio
     */
    protected $twilio;

    public function __construct(Twilio $twilio)
    {
        $this->twilio = $twilio;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\Twilio\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toTwilio($notifiable);

        if (is_string($message)) {
            $message = new Message($message);
        }

        if (! $to = $notifiable->routeNotificationFor('twilio')) {
            return;
        }

        $shouldSendMessage = event(new SendingMessage($notifiable, $notification), [], true) !== false;

        if (! $shouldSendMessage) {
            return;
        }

        try {
            $response = $this->twilio->account->messages->create(array(
                "From" => config('services.twilio.number'),
                "To" => "+".$to,
                "Body" => trim($message->content),
            ));
        } catch (TwilioRestException $e) {
            throw CouldNotSendNotification::twilioRespondedWithAnError($e->getMessage());
        }

        event(new MessageWasSent($notifiable, $notification));
    }
}
