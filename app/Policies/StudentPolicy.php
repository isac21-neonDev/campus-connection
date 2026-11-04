<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function view(User $user, Student $student): bool
    {
        if ($user->hasRole('administrator')) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $student->user_id === $user->id;
        }

        return $user->hasRole('lecturer') && $student->courses()
            ->where('lecturer_id', $user->id)
            ->exists();
    }
}