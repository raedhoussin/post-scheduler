<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
        public function up()
        {
            Schema::create('platform_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('platform_id')->constrained()->onDelete('cascade');
                $table->boolean('enabled')->default(true);
                $table->timestamps();
    
                $table->unique(['user_id', 'platform_id']); 
            });
        }
    
        public function down()
        {
            Schema::dropIfExists('platform_user');
        }
    };
    