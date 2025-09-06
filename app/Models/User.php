<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'first_name',
        'middle_name',
        'last_name',
        'contact_number',
        'email',
        'role_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;
        
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        
        $name .= ' ' . $this->last_name;
        
        return $name;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role && $this->role->name === $role;
    }

    /**
     * Check if the user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'Student';
    }

    /**
     * Check if the user is an administrator.
     */
    public function isAdministrator(): bool
    {
        return $this->role === 'Administrator';
    }

    /**
     * Check if the user is faculty.
     */
    public function isFaculty(): bool
    {
        return $this->role === 'Faculty';
    }

    /**
     * Check if the user is MCIIS staff.
     */
    public function isMCIISStaff(): bool
    {
        return $this->role === 'MCIIS Staff';
    }

    /**
     * Format contact number for display
     */
    public function getFormattedContactNumberAttribute(): ?string
    {
        if (!$this->contact_number) {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', $this->contact_number);
        
        if (strlen($clean) === 11 && substr($clean, 0, 2) === '09') {
            return '+63 ' . substr($clean, 1);
        }
        
        if (strlen($clean) === 12 && substr($clean, 0, 2) === '63') {
            return '+63 ' . substr($clean, 2);
        }
        
        return $this->contact_number;
    }

    /**
     * Format student ID for display
     */
    public function getFormattedStudentIdAttribute(): ?string
    {
        if (!$this->student_id) {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', $this->student_id);
        
        if (strlen($clean) >= 8) {
            $year = substr($clean, 0, 4);
            $number = substr($clean, 4);
            return $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
        }
        
        return $this->student_id;
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
}
