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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            // Relación con bancos
            $table->foreignId('banco_id')->constrained('bancos')->onDelete('cascade');

            // Datos base
            $table->string('codigo', 50)->unique(); // Ejemplo: P1_BCP, A2_BCP
            $table->string('tipo', 100);            // Ejemplo: Préstamo, Ahorro, Inversión
            $table->string('nombre_comercial', 150);
            $table->string('moneda', 10)->default('PEN');

            // Monto y plazo
            $table->decimal('monto_minimo', 15, 2)->nullable();
            $table->decimal('monto_maximo', 15, 2)->nullable();
            $table->decimal('monto_apertura', 15, 2)->nullable();
            $table->integer('plazo_minimo_meses')->nullable();
            $table->integer('plazo_maximo_meses')->nullable();

            // Tasas
            $table->decimal('tea_minimo', 8, 4)->nullable();
            $table->decimal('tea_maximo', 8, 4)->nullable();
            $table->decimal('tcea_referencial', 8, 4)->nullable();
            $table->decimal('trea_minima', 8, 4)->nullable();
            $table->decimal('trea_maxima', 8, 4)->nullable();

            // Seguros
            $table->decimal('seguro_desgravamen_mensual', 10, 6)->nullable();
            $table->string('seguro_extra', 255)->nullable();

            // Clasificación
            $table->enum('categoria', ['ACTIVO', 'PASIVO'])->default('ACTIVO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
