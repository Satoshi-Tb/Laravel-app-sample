<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('title')
                ->comment('ToDoタイトル');
            $table->text('memo')
                ->nullable()
                ->comment('詳細メモ');
            $table->dateTime('deadline')
                ->nullable()
                ->comment('締め切り日時');
            $table->dateTime('created_at')
                ->useCurrent()
                ->comment('登録日');
            $table->string('color')
                ->nullable()
                ->comment('色');
            $table->boolean('done')
                ->default(false)
                ->comment('完了状態');
            $table->foreignId('user_id')
                ->references('id')
                ->on('users');
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
