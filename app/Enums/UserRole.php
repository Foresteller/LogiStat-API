<?php

namespace App\Enums;

enum UserRole: string
{
    case CLIENT = 'client';
    case WAREHOUSE_ADMIN = 'warehouse_admin';
    case MANAGER = 'manager';
}
