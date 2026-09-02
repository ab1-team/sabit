<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\LaporanController;

class TestSubLaporanCommand extends Command
{
    protected $signature = 'test:sublaporan {tahunId}';
    protected $description = 'Test sub laporan endpoint';

    public function handle()
    {
        $tahunId = $this->argument('tahunId');

        tenancy()->initialize('demo');

        request()->merge([
            'tahun_akademik_id' => $tahunId,
        ]);

        $controller = app(LaporanController::class);
        $method = new \ReflectionMethod($controller, 'subLaporan');

        $response = $method->invoke($controller, 'pembayaran_spp');

        $this->line('Response class: ' . get_class($response));
        ob_start();
        echo $response;
        $body = ob_get_clean();
        $this->line('Length: ' . strlen($body));

        $count = substr_count($body, '<option');
        $this->line('Option count: ' . $count);
        $this->line('---');
        $this->line($body);
    }
}