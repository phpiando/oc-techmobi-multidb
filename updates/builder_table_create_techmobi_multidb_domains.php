<?php namespace Techmobi\Multidb\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class BuilderTableCreateTechmobiMultidbDomains extends Migration
{
    public function up()
    {
        Schema::create('techmobi_multidb_domains', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 400)->nullable();
            $table->boolean('waiting_sync')->default(1)->nullable();
            $table->boolean('has_sync_update')->nullable()->default(1);
            $table->boolean('has_user_db')->nullable();
            $table->string('db_host')->nullable();
            $table->string('db_port')->nullable();
            $table->string('db_user')->nullable();
            $table->string('db_pass')->nullable();
            $table->string('db_name')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('techmobi_multidb_domains');
    }
}
