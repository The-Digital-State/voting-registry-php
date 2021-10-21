<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var Invitation */
    protected $invitation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation->withoutRelations();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $ui = env('UI_URL');
        $poll = $this->invitation->poll;

        $subject = __('notification.invitation.subject', [
            'title' => $poll->title,
            'start' => $poll->start->format('l, j F Y'),
            'end' => $poll->end->format('l, j F Y'),
        ]);

        $message = __('notification.invitation.message', [
            'title' => $poll->title,
            'start' => $poll->start->format('l, j F Y'),
            'end' => $poll->end->format('l, j F Y'),
        ]);

        return (new MailMessage)
            ->subject($subject)
            ->line($message)
            ->line(__('notification.invitation.attention'))
            ->line(__('notification.invitation.attentionDescription'))
            ->line(__('notification.invitation.whenInvitationExpires'))
            ->action(__('notification.invitation.vote'), url("{$ui}/polls/{$poll->id}/preview?invitation={$this->invitation->token}"))
            ->line(__('notification.invitation.mistake'))
            ->line(__('notification.invitation.autoLetter'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
