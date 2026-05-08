<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreferencesToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Cỡ chữ mặc định là 14px
        $table->string('note_font_size')->default('14px')->after('dark_mode');
        // Màu ghi chú mặc định là màu vàng nhạt (#fff9c4)
        $table->string('note_color')->default('#fff9c4')->after('note_font_size');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['note_font_size', 'note_color']);
    });
}
}
