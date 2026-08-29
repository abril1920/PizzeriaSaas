<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->string('nombre', 80);
            $table->timestamps();
            $table->unique(['empresa_id', 'nombre']);
        });
        Schema::create('permisos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo', 100)->unique();
        });
        Schema::create('usuario_roles', function (Blueprint $table) {
            $table->foreignUuid('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignUuid('rol_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['usuario_id', 'rol_id']);
        });
        Schema::create('rol_permisos', function (Blueprint $table) {
            $table->foreignUuid('rol_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignUuid('permiso_id')->constrained('permisos')->cascadeOnDelete();
            $table->primary(['rol_id', 'permiso_id']);
        });
        Schema::create('categorias_producto', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->string('nombre', 100);
            $table->string('estado', 20)->default('ACTIVA');
            $table->timestamps();
            $table->unique(['empresa_id', 'nombre']);
        });
        Schema::create('productos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignUuid('categoria_id')->nullable()->constrained('categorias_producto')->nullOnDelete();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 14, 2);
            $table->string('estado', 20)->default('ACTIVO');
            $table->timestamps();
            $table->index(['empresa_id', 'estado']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('productos'); Schema::dropIfExists('categorias_producto');
        Schema::dropIfExists('rol_permisos'); Schema::dropIfExists('usuario_roles');
        Schema::dropIfExists('permisos'); Schema::dropIfExists('roles');
    }
};
