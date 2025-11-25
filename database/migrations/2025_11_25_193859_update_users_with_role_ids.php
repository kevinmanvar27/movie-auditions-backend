<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the role IDs
        $adminRole = Role::where('name', 'Admin')->first();
        $userRole = Role::where('name', 'User')->first();
        
        // Update existing users
        if ($adminRole) {
            DB::table('users')->where('role', 'admin')->update(['role_id' => $adminRole->id]);
        }
        
        if ($userRole) {
            DB::table('users')->where('role', 'user')->update(['role_id' => $userRole->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset role_id values
        DB::table('users')->update(['role_id' => null]);
    }
};
