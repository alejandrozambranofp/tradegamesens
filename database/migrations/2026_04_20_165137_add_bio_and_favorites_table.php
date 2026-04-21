<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('email');
        });

        Schema::create('favorite_guide_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('guide_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'guide_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_guide_user');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('bio');
        });
    }
};
