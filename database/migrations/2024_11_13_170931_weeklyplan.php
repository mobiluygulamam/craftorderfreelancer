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
     Schema::table('plans', function (Blueprint $table) {
          // Yeni sütunlar ekle

          if (!Schema::hasColumn('plans', 'weekly_price')) {
               $table->float('weekly_price', 30, 2)->default('0.00')->nullable();
           }
          if (!Schema::hasColumn('plans', 'plan_type')) {
               $table->text('plan_type')->nullable();
           }
 
      });

    

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
  Schema::table('plans', function (Blueprint $table) {
        if (Schema::hasColumn('plans', 'weekly_price')) {
            $table->dropColumn('weekly_price');
        }
        if (Schema::hasColumn('plans', 'plan_type')) {
            $table->dropColumn('plan_type');
        }
    });

    }
};
