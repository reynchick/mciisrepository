<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->user->email,
            subject: 'Your Account Has Been Created',
        );
    }

    public function content(): Content
    {
        // Map roles to emoji icons
        $roleEmojis = [
            'Faculty' => '🎓',
            'Student' => '👨‍🎓',
            'Administrator' => '👤',
            'MCIIS Staff' => '👥',
        ];

        // Build formatted roles with emojis
        $rolesWithEmojis = $this->user->roles->map(function ($role) use ($roleEmojis) {
            $emoji = $roleEmojis[$role->name] ?? '•';
            return "{$emoji} {$role->name}";
        })->toArray();

        return new Content(
            markdown: 'emails.account-created',
            with: [
                'rolesWithEmojis' => $rolesWithEmojis,
            ],
        );
    }
}
