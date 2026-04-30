<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('hotel_name')->default('Sun & Moon Riverside Hotel');
            $table->string('hotel_name_km')->nullable();
            $table->string('tagline')->nullable();
            $table->unsignedTinyInteger('star_rating')->default(4);
            $table->year('established_year')->nullable();
            $table->string('logo')->nullable();

            // Contact
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Cambodia');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Operations
            $table->time('checkin_time')->default('14:00:00');
            $table->time('checkout_time')->default('12:00:00');
            $table->string('currency', 10)->default('USD');
            $table->string('timezone')->default('Asia/Phnom_Penh');
            $table->decimal('vat_rate', 5, 2)->default(10.00);
            $table->unsignedSmallInteger('total_rooms')->default(0);

            // Social
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tripadvisor')->nullable();
            $table->string('booking_com')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
