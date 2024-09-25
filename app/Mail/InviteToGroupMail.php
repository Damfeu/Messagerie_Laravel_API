<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteToGroupMail extends Mailable
{
    use Queueable, SerializesModels;

    public $groupName;
    public $inviteeName;

    public function __construct($groupName, $inviteeName)
    {
        $this->groupName = $groupName;
        $this->inviteeName = $inviteeName;
    }

    public function build()
    {
        return $this->subject('Invitation à rejoindre le groupe ' . $this->groupName)
                    ->view('mail.invite_to_group')
                    ->with([
                        'groupName' => $this->groupName,
                        'inviteeName' => $this->inviteeName,
                    ]);
    }
}
