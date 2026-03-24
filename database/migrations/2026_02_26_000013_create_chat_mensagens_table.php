<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_mensagens', function (Blueprint $table) {
            $table->id();
            $table->string('remetente_email', 255)->index();
            $table->string('destinatario_email', 255)->index();
            $table->text('mensagem');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_mensagens');
    }
};
