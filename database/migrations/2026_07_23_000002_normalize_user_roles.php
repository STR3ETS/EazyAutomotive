<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The team-roles feature replaces the old generic 'editor' role with the
     * function-based roles. Any existing 'editor' becomes a 'sales' (Verkoper);
     * owner/admin are unchanged.
     */
    public function up(): void
    {
        DB::table('users')->where('role', 'editor')->update(['role' => 'sales']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'sales')->update(['role' => 'editor']);
    }
};
