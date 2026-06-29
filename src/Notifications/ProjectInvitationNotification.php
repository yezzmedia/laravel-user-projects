<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use YezzMedia\UserProjects\Models\Project;
use YezzMedia\UserProjects\Models\ProjectInvitation;

final class ProjectInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Project $project,
        public readonly ProjectInvitation $invitation,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('user-projects::ui.notification_invitation_subject', ['project' => $this->project->name]))
            ->greeting(__('user-projects::ui.notification_invitation_greeting'))
            ->line(__('user-projects::ui.notification_invitation_body', [
                'project' => $this->project->name,
                'role' => __("user-projects::ui.role_{$this->invitation->role}"),
            ]))
            ->action(__('user-projects::ui.notification_invitation_action'), url('/hub/projects'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'invitation_id' => $this->invitation->id,
            'role' => $this->invitation->role,
        ];
    }
}
