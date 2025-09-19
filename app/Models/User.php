<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomVerifyEmail;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Auditable;

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
        'must_change_password',
        'password_changed_at',
        'is_temporary_password',
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
            'password_changed_at' => 'datetime',
            'must_change_password' => 'boolean',
            'is_temporary_password' => 'boolean',
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

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user has a specific role
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
        return $this->hasRole('Student');
    }

    /**
     * Check if the user is an administrator.
     */
    public function isAdministrator(): bool
    {
        return $this->hasRole('Administrator');
    }

    /**
     * Check if the user is faculty.
     */
    public function isFaculty(): bool
    {
        return $this->hasRole('Faculty');
    }

    /**
     * Check if the user is MCIIS staff.
     */
    public function isMCIISStaff(): bool
    {
        return $this->hasRole('MCIIS Staff');
    }

    /**
     * Check if user is admin or staff
     */
    public function isAdminOrStaff(): bool
    {
        return $this->isAdministrator() || $this->isMCIISStaff();
    }
    
    /**
     * Check if user needs to change password
     */
    public function needsPasswordChange(): bool
    {
        return $this->must_change_password && $this->isAdminOrStaff();
    }

    /**
     * Mark that user has changed their password
     */
    public function markPasswordChanged(): void
    {
        $this->update([
            'must_change_password' => false,
            'is_temporary_password' => false,
            'password_changed_at' => now(),
        ]);
    }

    /**
     * Set temporary password for admin/staff users
     */
    public function setTemporaryPassword(string $password): void
    {
        $this->update([
            'password' => bcrypt($password),
            'must_change_password' => true,
            'is_temporary_password' => true,
            'password_changed_at' => null,
        ]);
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
