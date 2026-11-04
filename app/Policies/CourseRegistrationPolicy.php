<?php

namespace App\Policies;

use App\Models\CourseRegistration;
use App\Models\User;

class CourseRegistrationPolicy
{
    public function view(User $user, CourseRegistration $registration): bool
    {
        if ($user->hasRole('administrator')) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $registration->student->user_id === $user->id;
        }

        return $user->hasRole('lecturer')
            && $registration->course->lecturer_id === $user->id;
    }
}