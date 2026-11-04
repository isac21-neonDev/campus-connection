<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function view(User $user, Course $course): bool
    {
        return $user->hasAnyRole(['administrator', 'student'])
            || ($user->hasRole('lecturer') && $course->lecturer_id === $user->id);
    }
}