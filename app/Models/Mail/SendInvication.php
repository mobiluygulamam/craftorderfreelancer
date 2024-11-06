<?php

namespace App\Models\Mail;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Utility;


class SendInvication extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $project;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Project $project)
    {
        $this->user = $user;
        $this->project = $project;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Utility::setMailConfig();
        $setting = Utility::fetchAdminPaymentSetting();
        return $this->markdown('email.invitation')->subject('New Project Invitation - ' . $setting['app_name'] ? $setting['app_name'] : 'Taskly-Saas');
    }
}
