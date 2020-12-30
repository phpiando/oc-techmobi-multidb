<?php namespace Techmobi\Multidb\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateTechmobiMultidbMultisites extends Migration
{
    public function up()
    {
        Schema::create('techmobi_multidb_multisites', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('site_id');
            $table->integer('domain_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('techmobi_multidb_multisites');
    }
}
