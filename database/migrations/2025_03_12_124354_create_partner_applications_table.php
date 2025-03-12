<?php 
// database/migrations/xxxx_xx_xx_create_partner_applications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('partner_applications', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('company')->nullable();
            $table->string('partner_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('partner_applications');
    }
}


?>