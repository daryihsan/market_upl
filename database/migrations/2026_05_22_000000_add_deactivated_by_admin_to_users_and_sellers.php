<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'deactivated_by_admin')) {
                $table->boolean('deactivated_by_admin')->default(false)->after('status_akun');
            }
        });

        Schema::table('sellers', function (Blueprint $table) {
            if (! Schema::hasColumn('sellers', 'deactivated_by_admin')) {
                $table->boolean('deactivated_by_admin')->default(false)->after('status_akun');
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
            if (Schema::hasColumn('users', 'deactivated_by_admin')) {
                $table->dropColumn('deactivated_by_admin');
            }
        });

        Schema::table('sellers', function (Blueprint $table) {
            if (Schema::hasColumn('sellers', 'deactivated_by_admin')) {
                $table->dropColumn('deactivated_by_admin');
            }
        });
    }
};
