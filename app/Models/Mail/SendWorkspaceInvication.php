<?php

namespace App\Models\Mail;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Utility;

class SendWorkspaceInvication extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $workspace;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Workspace $workspace)
    {
        $this->user = $user;
        $this->workspace = $workspace;
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
        return $this->markdown('email.workspace_invitation')->subject('New Workspace Invitation - ' . $setting['app_name'] ? $setting['app_name'] : 'Taskly-Saas');
    }
}
