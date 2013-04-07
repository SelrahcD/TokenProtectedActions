<?php

use Illuminate\Database\Migrations\Migration;

class CreateTokenTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// create tokens table
		Schema::create('action_tokens', function($table)
		{
			$table->integer('user_id');
			$table->integer('action');
			$table->string('token');
			$table->timestamp('created_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// delete tokens table
		Schema::drop('action_tokens');
	}

}