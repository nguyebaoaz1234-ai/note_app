<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPinnedAtToNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notes', function (Blueprint $table) {
            // Thêm cột lưu thời gian ghim (cho phép rỗng nếu chưa ghim)
            $table->timestamp('pinned_at')->nullable()->after('is_pinned');
        });
    }

    public function down()
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('pinned_at');
        });
    }
}
