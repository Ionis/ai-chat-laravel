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
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('is_bot');
            $table->renameColumn('message', 'question');
            $table->text('answer')->after('question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->renameColumn('question', 'message');
            $table->boolean('is_bot')->default(false)->after('message');
            $table->dropColumn('answer');
        });
    }
};
