<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Vérifier si les colonnes n'existent pas déjà avant de les ajouter
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('location');
            }
            
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('bio');
            }
            
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['ACTIVE', 'PENDING_VALIDATION', 'REJECTED', 'SUSPENDED'])
                      ->default('ACTIVE')
                      ->after('avatar');
            }
            
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password')->after('email_verified_at');
            }
            
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 
                'bio', 
                'avatar', 
                'status', 
                'email_verified_at', 
                'password', 
                'remember_token'
            ]);
        });
    }
};