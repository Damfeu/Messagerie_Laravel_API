<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class GroupMemberAddedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $newMember;
    public $addedBy;
    public $groupName;
    // public $groupMembersEmails; // Tableau des emails des membres du groupe

    /**
     * Create a new message instance.
     */
    public function __construct($newMember, $addedBy, $groupName, )
    {
        $this->newMember = $newMember;
        $this->addedBy = $addedBy;
        $this->groupName = $groupName;
        // $this->groupMembersEmails = $groupMembersEmails; // Récupérer les emails des membres
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from('accounts@unetah.net', 'FlickTalk ')
                    // ->to($this->groupMembersEmails) // Envoyer à tous les membres du groupe
                    ->subject('Un nouveau membre a été ajouté au groupe')
                    ->view('mail.group_member_added')
                    ->with([
                        'newMember' => $this->newMember,
                        'addedBy' => $this->addedBy,
                        'groupName' => $this->groupName,
                        // 'groupMembersEmails'=> $this->groupMembersEmails
                    ]);
    }
}
