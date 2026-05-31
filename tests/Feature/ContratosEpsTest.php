<?php
// tests/Feature/ContratosEpsTest.php
namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Schema integration tests. Requires the dev database to be migrated.
 * Run: php artisan test tests/Feature/ContratosEpsTest.php
 */
class ContratosEpsTest extends TestCase
{
    public function test_contratos_eps_tables_exist()
    {
        $this->assertTrue(Schema::hasTable('contratos_eps'));
        $this->assertTrue(Schema::hasTable('planes_eps'));
        $this->assertTrue(Schema::hasTable('copagos_eps'));
        $this->assertTrue(Schema::hasTable('paciente_planes_eps'));
    }

    public function test_citas_has_autorizacion_columns()
    {
        $this->assertTrue(Schema::hasColumn('citas', 'numero_autorizacion'));
        $this->assertTrue(Schema::hasColumn('citas', 'copago_cobrado'));
    }

    public function test_contratos_eps_index_requires_auth()
    {
        $this->markTestSkipped('Route added in Task 3');
        $response = $this->get('/Administracion/ContratosEps');
        $response->assertRedirect('/');
    }

    public function test_listar_contratos_requires_auth()
    {
        $this->markTestSkipped('Route added in Task 3');
        $response = $this->postJson('/contratosEps/listarContratos');
        $response->assertStatus(401);
    }

    public function test_guardar_plan_paciente_requires_auth()
    {
        $this->markTestSkipped('Route added in Task 3');
        $response = $this->postJson('/pacientes/guardarPlanEps');
        $response->assertStatus(401);
    }
}
