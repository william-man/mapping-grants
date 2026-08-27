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
        Schema::create('grants', function (Blueprint $table) {
            $table->id();
            $table->string('grantsID');
            $table->text('title');
            $table->text('description');
            $table->char('currency', 3);
            $table->decimal('amountAwarded', 15, 2);
            $table->date('awardDate');

            $table->json('fundingOrganization');
            $table->json('recipientOrganization')->nullable();
            $table->json('recipientIndividual')->nullable();
            $table->string('recipientPostcode')->nullable();

            $table->index('grantsID');
            $table->index('recipientPostcode');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grants');
    }
};
