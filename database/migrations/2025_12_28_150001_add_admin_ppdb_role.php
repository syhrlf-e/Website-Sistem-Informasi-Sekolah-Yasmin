<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Add admin_ppdb role to users table
 * 
 * Role system:
 * - super_admin: Full access, manage users
 * - admin: All content except users management
 * - admin_ppdb: Only PPDB management access
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Note: Since we're using string column for role (not enum),
        // we just need to update validation in controllers and middleware.
        // No schema change needed, but this migration documents the addition.

        // Optional: If you want to enforce enum at DB level, uncomment below
        // But Laravel string is more flexible for adding new roles
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed - role is just a string value
    }
};
