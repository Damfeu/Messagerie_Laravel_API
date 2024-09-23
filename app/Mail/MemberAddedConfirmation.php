<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class MemberAddedConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $memberName;
    public $groupName;

    /**
     * Create a new message instance.
     */
    public function __construct($memberName, $groupName)
    {
        $this->memberName = $memberName;
        $this->groupName = $groupName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Confirmation d’ajout au groupe')
                    ->from(new Address('accounts@unetah.net', 'EcoCollecte'))
                    ->view('mail.member_confirmation') // La vue de l'email
                    ->with([
                        'memberName' => $this->memberName,
                        'groupName' => $this->groupName,
                    ]);
    }
}
