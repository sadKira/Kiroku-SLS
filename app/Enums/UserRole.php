<?php

namespace App\Enums;

enum UserRole: String
{
    case Logger = 'logger';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';
}
