<?php

declare(strict_types=1);

namespace YezzMedia\UserProjects\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use YezzMedia\UserProjects\Models\Project;

final class ProjectRoleChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Project $project,
        public readonly string $newRole,
        public readonly ?string $oldRole = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('user-projects::ui.notification_role_subject', ['project' => $this->project->name]))
            ->greeting(__('user-projects::ui.notification_role_greeting'))
            ->line(__('user-projects::ui.notification_role_body', [
                'project' => $this->project->name,
                'role' => __("user-projects::ui.role_{$this->newRole}"),
            ]))
            ->action(__('user-projects::ui.notification_role_action'), url('/hub/projects?project='.$this->project->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'old_role' => $this->oldRole,
            'new_role' => $this->newRole,
        ];
    }
}
