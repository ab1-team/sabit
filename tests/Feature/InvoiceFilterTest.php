<?php

namespace Tests\Feature;

use App\Models\Tenant\TenantAdminUser;
use App\Models\Tenant\TenantInvoice;
use App\Models\Tenant\TenantRekening;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Filter status (Lunas / Belum Lunas) di halaman invoice konsol pusat
 * + redirect setelah pembayaran kembali ke halaman invoice.
 */
class InvoiceFilterTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $user = TenantAdminUser::query()->first();
        $this->actingAs($user, 'tenant');
    }

    private function makeInvoice(string $status, float $jumlah = 1000): TenantInvoice
    {
        return TenantInvoice::create([
            'tenant_id'        => 'demo',
            'jenis_pembayaran' => TenantInvoice::JENIS_PEMBAYARAN[0],
            'tgl_invoice'      => now()->toDateString(),
            'tgl_lunas'        => now()->addDays(7)->toDateString(),
            'status'           => $status,
            'jumlah'           => $jumlah,
            'user_id'          => TenantAdminUser::query()->first()->id,
        ]);
    }

    /**
     * Jumlah invoice paid yang sudah ada sebelum test.
     *
     * `DatabaseTransactions` hanya meng-rollback koneksi default, sedangkan
     * tabel invoice memakai koneksi `central`. Karena itu jumlah absolut
     * tidak bisa diasumsikan — harus dihitung relatif.
     */
    private function totalPaid(): int
    {
        return TenantInvoice::query()->where('status', TenantInvoice::STATUS_PAID)->count();
    }

    private function totalAll(): int
    {
        return TenantInvoice::query()->count();
    }

    public function test_halaman_invoice_tidak_lagi_punya_filter_jenis_dan_jatuh_tempo(): void
    {
        $response = $this->get('/invoice');

        $response->assertOk();
        // Filter jenis pembayaran & jatuh tempo dihapus.
        $response->assertDontSee('id="filter-jenis"', false);
        $response->assertDontSee('id="filter-due"', false);
        $response->assertDontSee('id="filter-reset"', false);
        // Filter status dipindah ke panel sekolah.
        $response->assertSee('id="filter-status"', false);
    }

    public function test_filter_status_menampilkan_semua_status_serta_paid_dan_unpaid(): void
    {
        $response = $this->get('/invoice');

        $response->assertOk();
        $response->assertSee('value="__all__"', false);
        $response->assertSee('value="paid"', false);
        $response->assertSee('value="unpaid"', false);
    }

    public function test_data_endpoint_memfilter_status_paid(): void
    {
        $before = $this->totalPaid();

        $paid = $this->makeInvoice(TenantInvoice::STATUS_PAID);
        $this->makeInvoice(TenantInvoice::STATUS_UNPAID);

        $response = $this->getJson('/invoice/data?status_filter=paid');

        $response->assertOk();
        $response->assertJsonPath('recordsFiltered', $before + 1);

        // Semua baris yang dikembalikan harus berstatus paid.
        $ids = array_column($response->json('data'), 'id');
        $this->assertContains($paid->id, $ids);

        $statuses = TenantInvoice::query()
            ->whereIn('id', $ids)
            ->pluck('status')
            ->unique()
            ->all();

        $this->assertSame([TenantInvoice::STATUS_PAID], $statuses);
    }

    public function test_data_endpoint_menampilkan_semua_bila_tanpa_filter(): void
    {
        $before = $this->totalAll();

        $this->makeInvoice(TenantInvoice::STATUS_PAID);
        $this->makeInvoice(TenantInvoice::STATUS_UNPAID);

        $response = $this->getJson('/invoice/data');

        $response->assertOk();
        $response->assertJsonPath('recordsFiltered', $before + 2);
    }

    public function test_data_endpoint_menampilkan_semua_bila_filter_tidak_valid(): void
    {
        $before = $this->totalAll();

        $this->makeInvoice(TenantInvoice::STATUS_PAID);
        $this->makeInvoice(TenantInvoice::STATUS_UNPAID);

        $response = $this->getJson('/invoice/data?status_filter=bogus');

        $response->assertOk();
        $response->assertJsonPath('recordsFiltered', $before + 2);
    }

    public function test_halaman_invoice_menandai_filter_dari_url(): void
    {
        $response = $this->get('/invoice?status_filter=paid');

        $response->assertOk();
        // Opsi "paid" harus terpilih.
        $this->assertStringContainsString(
            '<option value="paid" selected>',
            $response->getContent()
        );
    }

    public function test_pembayaran_redirect_kembali_ke_halaman_invoice(): void
    {
        $invoice = $this->makeInvoice(TenantInvoice::STATUS_UNPAID, 5000);
        $rekening = TenantRekening::query()->first();

        $response = $this->post('/transaksi', [
            'idv'            => $invoice->id,
            'rekening_debit' => $rekening->kd_rekening,
            'jumlah'         => 5000,
            'mark_paid'      => 1,
            'redirect_to'    => '/invoice',
        ]);

        $response->assertRedirect('/invoice');
        $response->assertSessionHas('success');
    }

    public function test_pembayaran_tanpa_redirect_to_kembali_ke_transaksi(): void
    {
        $invoice = $this->makeInvoice(TenantInvoice::STATUS_UNPAID, 5000);
        $rekening = TenantRekening::query()->first();

        $response = $this->post('/transaksi', [
            'idv'            => $invoice->id,
            'rekening_debit' => $rekening->kd_rekening,
            'jumlah'         => 5000,
            'mark_paid'      => 1,
        ]);

        $response->assertRedirect(route('tenant.transaksi.index'));
    }

    public function test_redirect_eksternal_ditolak(): void
    {
        $invoice = $this->makeInvoice(TenantInvoice::STATUS_UNPAID, 5000);
        $rekening = TenantRekening::query()->first();

        $response = $this->post('/transaksi', [
            'idv'            => $invoice->id,
            'rekening_debit' => $rekening->kd_rekening,
            'jumlah'         => 5000,
            'mark_paid'      => 1,
            'redirect_to'    => 'https://evil.example.com/steal',
        ]);

        $response->assertRedirect(route('tenant.transaksi.index'));
    }

    public function test_redirect_protocol_relative_ditolak(): void
    {
        $invoice = $this->makeInvoice(TenantInvoice::STATUS_UNPAID, 5000);
        $rekening = TenantRekening::query()->first();

        $response = $this->post('/transaksi', [
            'idv'            => $invoice->id,
            'rekening_debit' => $rekening->kd_rekening,
            'jumlah'         => 5000,
            'mark_paid'      => 1,
            'redirect_to'    => '//evil.example.com/steal',
        ]);

        $response->assertRedirect(route('tenant.transaksi.index'));
    }
}
