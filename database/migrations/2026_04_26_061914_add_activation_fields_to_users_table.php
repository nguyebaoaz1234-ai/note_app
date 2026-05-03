<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActivationFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Kiểm tra: Nếu chưa có cột is_active thì mới tạo
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(0); 
            }
            
            // Kiểm tra: Nếu chưa có cột activation_token thì mới tạo
            if (!Schema::hasColumn('users', 'activation_token')) {
                $table->string('activation_token')->nullable(); 
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'activation_token']);
        });
    }
}
