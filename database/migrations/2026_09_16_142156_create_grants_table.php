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

            $table->string('fundingOrganisation');
            $table->foreign('fundingOrganisation')->references('organisationID')->on('funding_organisations')->onDelete('cascade');

            $table->string('recipientOrganisation')->nullable();
            $table->foreign('recipientOrganisation')->references('organisationID')->on('recipient_organisations')->onDelete('cascade');
          
            $table->string('recipientIndividual')->nullable();
            $table->foreign('recipientIndividual')->references('individualID')->on('recipient_individuals')->onDelete('cascade');

            $table->index('grantsID');
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
