<?php

namespace Tests\Feature;

use App\Models\Rekening;
use App\Models\Saldo;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class JurnalUmumTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $this->actingAs(User::factory()->create());
    }

    private function jurnalPayload(string $tanggal, string $sumber, string $disimpan, string $nominal, string $ket): array
    {
        return [
            'transaksi'   => 'jurnal_umum',
            'tanggal'     => $tanggal,
            'sumber_dana' => $sumber,
            'disimpan_ke' => $disimpan,
            'jurnal_umum' => ['keterangan' => $ket, 'nominal' => $nominal],
            'beli_inventaris' => [],
            'hapus_inventaris' => [],
        ];
    }

    public function test_jurnal_umum_store_inserts_transaksi_and_updates_saldo(): void
    {
        $sumber    = Rekening::where('kode_akun', '1.1.01.01')->firstOrFail();
        $disimpan  = Rekening::where('kode_akun', '1.1.03.01')->firstOrFail();

        $this->postJson('/app/transaksi', $this->jurnalPayload(
            tanggal: '2024-09-15',
            sumber: $sumber->kode_akun,
            disimpan: $disimpan->kode_akun,
            nominal: '500.000',
            ket: 'Setoran awal piutang'
        ))->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('transaksi', [
            'rekening_debit' => $disimpan->kode_akun,
            'rekening_kredit' => $sumber->kode_akun,
            'keterangan' => 'Setoran awal piutang',
        ]);

        $this->assertDatabaseHas('saldo', [
            'kode_akun' => $disimpan->kode_akun,
            'bulan' => 9, 'tahun' => 2024,
            'debit' => '500.00', 'kredit' => '0.00',
        ]);

        $this->assertDatabaseHas('saldo', [
            'kode_akun' => $sumber->kode_akun,
            'bulan' => 9, 'tahun' => 2024,
            'debit' => '0.00', 'kredit' => '500.00',
        ]);
    }

    public function test_jurnal_umum_store_validation_requires_core_fields(): void
    {
        $this->postJson('/app/transaksi', [
            'transaksi' => 'jurnal_umum',
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['tanggal', 'sumber_dana', 'disimpan_ke']);
    }

    public function test_index_menampilkan_total_saldo_setelah_jurnal_umum(): void
    {
        $sumber   = Rekening::where('kode_akun', '1.1.01.01')->firstOrFail();
        $disimpan = Rekening::where('kode_akun', '1.1.03.01')->firstOrFail();

        $this->postJson('/app/transaksi', $this->jurnalPayload(
            tanggal: '2024-09-15',
            sumber: $sumber->kode_akun,
            disimpan: $disimpan->kode_akun,
            nominal: '300.000',
            ket: 'Tes saldo'
        ))->assertOk();

        $this->get('/app/transaksi')
            ->assertOk()
            ->assertViewHas('totalSaldo', 0.0);
    }

    public function test_jurnal_umum_destroy_soft_deletes_row(): void
    {
        $sumber   = Rekening::where('kode_akun', '1.1.01.01')->firstOrFail();
        $disimpan = Rekening::where('kode_akun', '1.1.03.01')->firstOrFail();

        $this->postJson('/app/transaksi', $this->jurnalPayload(
            tanggal: '2024-09-15',
            sumber: $sumber->kode_akun,
            disimpan: $disimpan->kode_akun,
            nominal: '100.000',
            ket: 'Akan dihapus'
        ))->assertOk();

        $trx = Transaksi::where('keterangan', 'Akan dihapus')->firstOrFail();

        $this->deleteJson("/app/transaksi/jurnal-umum/{$trx->id}")
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('transaksi', ['id' => $trx->id]);
    }
}
