<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDetailsToNoteUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('note_user', function (Blueprint $table) {
            // Chỉ thêm cột permission, không thêm timestamps nữa vì đã có sẵn
            $table->string('permission')->default('read')->after('user_id'); 
        });
    }

    public function down()
    {
        Schema::table('note_user', function (Blueprint $table) {
            // Tương ứng khi rollback thì chỉ drop cột permission
            $table->dropColumn('permission');
        });
    }
}
