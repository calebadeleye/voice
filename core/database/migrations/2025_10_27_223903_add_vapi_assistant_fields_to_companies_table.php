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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('assistant_id')->nullable()->after('ai_name');
            $table->text('welcome_message')->nullable()->after('assistant_description');
      
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
              $table->dropColumn([
                'assistant_id','welcome_message',
            ]);
        });
    }
};
