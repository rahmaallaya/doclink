
<?php
// database/migrations/2025_01_01_000000_update_users_for_auth.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','password'))           $table->string('password')->after('email');
            if (!Schema::hasColumn('users','email_verified_at'))   $table->timestamp('email_verified_at')->nullable();
            if (!Schema::hasColumn('users','status'))              $table->string('status')->default('ACTIVE');
            if (!Schema::hasColumn('users','remember_token'))      $table->rememberToken();
            $table->index(['email','role','status']);
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            // rollback minimal
            if (Schema::hasColumn('users','remember_token'))  $table->dropColumn('remember_token');
            if (Schema::hasColumn('users','status'))          $table->dropColumn('status');
            if (Schema::hasColumn('users','email_verified_at'))$table->dropColumn('email_verified_at');
            // (On ne droppe pas 'password' si déjà utilisé)
        });
    }
};
