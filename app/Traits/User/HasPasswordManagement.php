<?php

namespace App\Traits\User;

trait HasPasswordManagement
{
    public function needsPasswordChange(): bool
    {
        return $this->must_change_password && $this->isAdminOrStaff();
    }

    public function markPasswordChanged(): void
    {
        $this->forceFill([
            'must_change_password' => false,
            'is_temporary_password' => false,
            'password_changed_at' => now(),
        ])->save();
    }

    public function setTemporaryPassword(string $password): void
    {
        $this->forceFill([
            'password' => $password,
            'must_change_password' => true,
            'is_temporary_password' => true,
            'password_changed_at' => null,
        ])->save();
    }
}
