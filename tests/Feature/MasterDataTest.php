<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_jurusan_can_be_created(): void
    {
        $payload = [
            'kode_jurusan' => 'RPL',
            'nama'         => 'Rekayasa Perangkat Lunak',
            'status'       => 'aktif',
        ];

        $this->postJson('/app/jurusan', $payload)
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('jurusan', ['kode_jurusan' => 'RPL']);
    }

    public function test_jurusan_requires_kode_and_nama(): void
    {
        $this->postJson('/app/jurusan', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['kode_jurusan', 'nama', 'status']);
    }

    public function test_jurusan_kode_must_be_unique(): void
    {
        Jurusan::create(['kode_jurusan' => 'RPL', 'nama' => 'A', 'status' => 'aktif']);

        $this->postJson('/app/jurusan', [
            'kode_jurusan' => 'RPL',
            'nama'         => 'B',
            'status'       => 'aktif',
        ])->assertStatus(422)->assertJsonValidationErrors(['kode_jurusan']);
    }

    public function test_tahun_akademik_aktifkan_deactivates_others(): void
    {
        $a = TahunAkademik::create(['nama_tahun' => '2024/2025', 'keterangan' => '-', 'status' => 'aktif']);
        $b = TahunAkademik::create(['nama_tahun' => '2025/2026', 'keterangan' => '-', 'status' => 'nonaktif']);

        $this->postJson("/app/tahun-akademik/{$b->id}/aktifkan")
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('tahun_akademik', ['id' => $a->id, 'status' => 'nonaktif']);
        $this->assertDatabaseHas('tahun_akademik', ['id' => $b->id, 'status' => 'aktif']);
    }

    public function test_tahun_akademik_store_deactivates_existing_active(): void
    {
        TahunAkademik::create(['nama_tahun' => '2024/2025', 'keterangan' => '-', 'status' => 'aktif']);

        $this->postJson('/app/tahun-akademik', [
            'nama_tahun' => '2025/2026',
            'keterangan' => '-',
            'status'     => 'aktif',
        ])->assertOk();

        $this->assertDatabaseMissing('tahun_akademik', ['nama_tahun' => '2024/2025', 'status' => 'aktif']);
        $this->assertDatabaseHas('tahun_akademik', ['nama_tahun' => '2025/2026', 'status' => 'aktif']);
    }
}

