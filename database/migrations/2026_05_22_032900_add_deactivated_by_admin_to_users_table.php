<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeactivatedByAdminToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'deactivated_by_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('deactivated_by_admin')->default(false)->after('status_akun');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'deactivated_by_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('deactivated_by_admin');
            });
        }
    }
}
