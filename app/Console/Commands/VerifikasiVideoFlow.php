<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Landing\VideoLanding;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Verifikasi end-to-end alur simpan Video ke DB & tampilannya.
 *
 * Mensimulasikan submit form admin (YouTube + Local upload) lewat controller
 * yang sama persis dengan route web, lalu memeriksa:
 *   1. Row tersimpan di tabel `lp_video`
 *   2. File video/poster tersimpan di storage disk 'public'
 *   3. Data bisa di-load ulang via model
 */
class VerifikasiVideoFlow extends Command
{
    protected $signature = 'verify:video-flow {--reset : Hapus data VideoLanding demo sebelum test}';

    protected $description = 'End-to-end verifikasi: submit Video (YouTube + local upload) → DB → display URL';

    public function handle(): int
    {
        $tenant = Tenant::find('demo');
        if (!$tenant) {
            $this->error('Tenant demo tidak ditemukan.');
            return self::FAILURE;
        }

        tenancy()->initialize($tenant);
        $this->info("== Tenant: {$tenant->id} ({$tenant->nama_sekolah}) ==");

        if ($this->option('reset')) {
            $deleted = VideoLanding::query()->delete();
            $this->warn("Reset: menghapus {$deleted} baris VideoLanding.");
        }

        $before = VideoLanding::count();
        $this->line("Rows sebelum: {$before}");

        // ========== CASE 1: Submit YouTube ==========
        $this->newLine();
        $this->info('-- CASE 1: Submit YouTube URL --');
        $ytRowId = $this->simulateYoutubeSubmit();
        if (!$ytRowId) {
            $this->error('CASE 1 GAGAL. Batalkan.');
            tenancy()->end();
            return self::FAILURE;
        }

        // ========== CASE 2: Submit Local File ==========
        $this->newLine();
        $this->info('-- CASE 2: Submit Local Upload (mp4 tanpa poster) --');
        $localRowId = $this->simulateLocalSubmit();
        if (!$localRowId) {
            $this->error('CASE 2 GAGAL.');
            tenancy()->end();
            return self::FAILURE;
        }

        // ========== CASE 3: Submit Local File + Poster ==========
        $this->newLine();
        $this->info('-- CASE 3: Submit Local Upload + Poster JPG --');
        $localWithPosterId = $this->simulateLocalSubmit(withPoster: true);
        if (!$localWithPosterId) {
            $this->error('CASE 3 GAGAL.');
            tenancy()->end();
            return self::FAILURE;
        }

        // ========== CASE 4: Update Local Video (replace file + add poster) ==========
        $this->newLine();
        $this->info('-- CASE 4: Update Local Video (replace file + add poster) --');
        $updateOk = $this->simulateLocalUpdate($localRowId);
        if (!$updateOk) {
            $this->error('CASE 4 GAGAL.');
            tenancy()->end();
            return self::FAILURE;
        }

        // ========== CASE 5: Destroy Local Video ==========
        $this->newLine();
        $this->info('-- CASE 5: Destroy Local Video --');
        $destroyOk = $this->simulateLocalDestroy($localRowId);
        if (!$destroyOk) {
            $this->error('CASE 5 GAGAL.');
            tenancy()->end();
            return self::FAILURE;
        }

        // ========== VERIFIKASI ==========
        $this->newLine();
        $this->info('-- VERIFIKASI --');
        $after = VideoLanding::count();
        // CASES 1, 2, 3 menambah 3 baris. CASE 5 (destroy) menghapus satu baris.
        // Net delta = +2. CASE 4 (update) tidak menambah/menghapus.
        $this->line("Rows sesudah: {$after} (delta: " . ($after - $before) . ', expected +2 karena ada 1 destroy)');
        if (($after - $before) !== 2) {
            $this->error('Expected delta +2 (= 3 stores - 1 destroy), got ' . ($after - $before));
            tenancy()->end();
            return self::FAILURE;
        }

        $this->dumpRow($ytRowId, 'YT');
        $this->dumpRow($localRowId, 'LOCAL-NO-POSTER');
        $this->dumpRow($localWithPosterId, 'LOCAL-WITH-POSTER');

        $this->newLine();
        $this->info('Cek file storage:');
        $disk = Storage::disk('public');
        $videos = collect($disk->allFiles('landing/videos'));
        $this->line('Files in landing/videos/: ' . (empty($videos) ? 'KOSONG' : $videos->count()));
        foreach ($videos as $f) {
            $this->line('  - ' . $f . ' (' . $disk->size($f) . ' bytes)');
        }
        $posters = collect($disk->allFiles('landing/videos/posters'));
        $this->line('Files in landing/videos/posters/: ' . (empty($posters) ? 'KOSONG' : $posters->count()));
        foreach ($posters as $f) {
            $this->line('  - ' . $f . ' (' . $disk->size($f) . ' bytes)');
        }

        $this->newLine();
        $this->info('Cek URL publik (Storage::disk(public)->url):');
        foreach (VideoLanding::orderBy('id')->get() as $v) {
            $url = $v->embed_url;
            $thumb = $v->display_thumb;
            $this->line(sprintf(
                "  #%d [%s] %s | embed=%s | thumb=%s",
                $v->id,
                $v->isLocal() ? 'LOCAL' : 'YT',
                $v->title,
                $url ?: '(null)',
                $thumb ?: '(null)'
            ));
        }

        $this->newLine();
        $this->renderPublicPage();

        tenancy()->end();
        $this->info('OK.');
        return self::SUCCESS;
    }

    private function simulateYoutubeSubmit(): ?int
    {
        $req = Request::create('/app/admin-landing/videos', 'POST', [
            'title' => 'Tes Youtube Verifikasi ' . now()->format('H:i:s'),
            'description' => 'Verifikasi otomatis via Artisan command.',
            'source' => 'youtube',
            'youtube_url' => 'https://youtu.be/aqz-KE-bpKQ',
            'is_published' => '1',
        ]);
        $req->headers->set('X-Requested-With', 'XMLHttpRequest');
        $req->headers->set('Accept', 'application/json');

        // Login pakai user landing (id=7, password apa pun di testing)
        $user = DB::table('users')->where('username', 'landing')->first();
        if (!$user) {
            $this->error('User landing tidak ditemukan.');
            return null;
        }
        Auth::loginUsingId($user->id);

        // Panggil controller method langsung
        $controller = new \App\Http\Controllers\AdminLandingController();
        try {
            $resp = $controller->videoStore($req);
            $status = method_exists($resp, 'getStatusCode') ? $resp->getStatusCode() : 200;
            $data = method_exists($resp, 'getData') ? $resp->getData(true) : [];
            $this->line('Response status: ' . $status);
            $this->line('Response body:   ' . json_encode($data));
            if (($data['success'] ?? false) !== true) {
                return null;
            }
        } catch (\Throwable $e) {
            $this->error('Exception: ' . $e->getMessage());
            return null;
        }

        $row = VideoLanding::orderByDesc('id')->first();
        $this->line('Row tersimpan: id=' . $row->id . ' source=' . $row->source . ' youtube_url=' . $row->youtube_url);
        return $row->id;
    }

    private function simulateLocalSubmit(bool $withPoster = false): ?int
    {
        // Buat file mp4 dummy — header minimal "mp4 valid" + padding data.
        $tmpDir = storage_path('framework/testing-tmp');
        if (!is_dir($tmpDir)) @mkdir($tmpDir, 0777, true);

        // Tulis file mp4 dummy + (opsional) jpg poster
        $mp4Path = $tmpDir . '/sample-' . uniqid() . '.mp4';
        // Header MP4 ('mp42 / 'isom' / ...). Tulis 200 KB data kosong minimum:
        file_put_contents($mp4Path, $this->makeDummyMp4Bytes());

        $uploadedVideo = new UploadedFile($mp4Path, basename($mp4Path), 'video/mp4', null, true);

        $files = ['video_file' => $uploadedVideo];

        if ($withPoster) {
            $jpgPath = $tmpDir . '/poster-' . uniqid() . '.jpg';
            file_put_contents($jpgPath, $this->makeDummyJpgBytes());
            $uploadedPoster = new UploadedFile($jpgPath, basename($jpgPath), 'image/jpeg', null, true);
            $files['video_poster'] = $uploadedPoster;
        }

        $req = Request::create('/app/admin-landing/videos', 'POST', [
            'title' => ($withPoster ? 'Tes Lokal + Poster ' : 'Tes Lokal ') . now()->format('H:i:s'),
            'description' => 'Lokal upload otomatis.',
            'source' => 'local',
            'is_published' => '1',
        ], [], $files);
        $req->headers->set('X-Requested-With', 'XMLHttpRequest');
        $req->headers->set('Accept', 'application/json');

        $user = DB::table('users')->where('username', 'landing')->first();
        Auth::loginUsingId($user->id);

        $controller = new \App\Http\Controllers\AdminLandingController();
        try {
            $resp = $controller->videoStore($req);
            $data = method_exists($resp, 'getData') ? $resp->getData(true) : [];
            $this->line('Response body: ' . json_encode($data));
            if (($data['success'] ?? false) !== true) {
                return null;
            }
        } catch (\Throwable $e) {
            $this->error('Exception: ' . $e->getMessage());
            return null;
        }

        $row = VideoLanding::orderByDesc('id')->first();
        $this->line('Row tersimpan: id=' . $row->id . ' source=' . $row->source . ' file_path=' . $row->file_path . ' poster=' . ($row->poster ?? 'null'));
        return $row->id;
    }

    private function simulateLocalUpdate(int $id): bool
    {
        $model = VideoLanding::findOrFail($id);
        $oldFilePath = $model->file_path;

        $tmpDir = storage_path('framework/testing-tmp');
        $newMp4Path = $tmpDir . '/replaced-' . uniqid() . '.mp4';
        file_put_contents($newMp4Path, $this->makeDummyMp4Bytes());
        $newJpgPath = $tmpDir . '/poster-replaced-' . uniqid() . '.jpg';
        file_put_contents($newJpgPath, $this->makeDummyJpgBytes());

        $files = [
            'video_file' => new UploadedFile($newMp4Path, basename($newMp4Path), 'video/mp4', null, true),
            'video_poster' => new UploadedFile($newJpgPath, basename($newJpgPath), 'image/jpeg', null, true),
        ];

        $req = Request::create("/app/admin-landing/videos/{$id}", 'POST', [
            '_method' => 'PUT',
            'title' => 'Tes Lokal (updated) ' . now()->format('H:i:s'),
            'description' => 'Setelah update.',
            'source' => 'local',
            'is_published' => '1',
        ], [], $files);
        $req->headers->set('X-Requested-With', 'XMLHttpRequest');
        $req->headers->set('Accept', 'application/json');

        Auth::loginUsingId(DB::table('users')->where('username', 'landing')->value('id'));
        $controller = new \App\Http\Controllers\AdminLandingController();
        try {
            $resp = $controller->videoUpdate($req, $id);
            $data = method_exists($resp, 'getData') ? $resp->getData(true) : [];
            $this->line('Update response: ' . json_encode($data));
            if (($data['success'] ?? false) !== true) {
                return false;
            }
        } catch (\Throwable $e) {
            $this->error('Exception update: ' . $e->getMessage());
            return false;
        }

        $fresh = VideoLanding::find($id);
        $this->line("Update row: file_path={$fresh->file_path} (was: {$oldFilePath}) | poster={$fresh->poster}");
        $this->line('Old file still exists on disk? ' . (Storage::disk('public')->exists($oldFilePath) ? 'YES (BUG)' : 'NO (OK)'));
        $this->line('New file exists on disk? ' . (Storage::disk('public')->exists($fresh->file_path) ? 'YES (OK)' : 'NO (BUG)'));
        return true;
    }

    private function simulateLocalDestroy(int $id): bool
    {
        $model = VideoLanding::find($id);
        $filePath = $model->file_path;
        $poster = $model->poster;

        $req = Request::create("/app/admin-landing/videos/{$id}", 'POST', ['_method' => 'DELETE']);
        $req->headers->set('X-Requested-With', 'XMLHttpRequest');
        $req->headers->set('Accept', 'application/json');

        Auth::loginUsingId(DB::table('users')->where('username', 'landing')->value('id'));
        $controller = new \App\Http\Controllers\AdminLandingController();
        try {
            $resp = $controller->videoDestroy($req, $id);
            $data = method_exists($resp, 'getData') ? $resp->getData(true) : [];
            $this->line('Destroy response: ' . json_encode($data));
            if (($data['success'] ?? false) !== true) {
                return false;
            }
        } catch (\Throwable $e) {
            $this->error('Exception destroy: ' . $e->getMessage());
            return false;
        }

        $this->line('Row still exists? ' . (VideoLanding::find($id) ? 'YES (BUG)' : 'NO (OK)'));
        $this->line('File still on disk? ' . (Storage::disk('public')->exists($filePath) ? 'YES (BUG)' : 'NO (OK)'));
        $this->line('Poster still on disk? ' . (Storage::disk('public')->exists($poster) ? 'YES (BUG)' : 'NO (OK)'));
        return true;
    }

    private function dumpRow(int $id, string $label): void
    {
        $v = VideoLanding::find($id);
        if (!$v) {
            $this->line("[{$label}] Row #{$id} not found (sengaja dihapus oleh CASE 5 — OK).");
            return;
        }
        $this->line(sprintf(
            "[%s] #%d title='%s' source=%s is_published=%s youtube_url=%s file_path=%s poster=%s youtube_id=%s embed_url=%s display_thumb=%s",
            $label,
            $v->id,
            $v->title,
            $v->source,
            $v->is_published ? 'true' : 'false',
            $v->youtube_url ?? 'null',
            $v->file_path ?? 'null',
            $v->poster ?? 'null',
            $v->youtube_id ?? 'null',
            $v->embed_url ?? 'null',
            $v->display_thumb ?? 'null'
        ));
    }

    private function renderPublicPage(): void
    {
        // Render halaman publik /video untuk mensimulasikan kunjungan user di subdomain landing.
        $controller = new \App\Http\Controllers\HalamanPublikController();
        $resp = $controller->videos();
        $html = $resp->render();

        $rows = VideoLanding::published()->orderByDesc('id')->get();

        // Count via class match (CSS rules masuk substring count, jadi bedakan)
        preg_match_all('/<button[^>]*class="[^"]*lp-video-trigger[^"]*"[^>]*>/i', $html, $bm);
        $triggers = count($bm[0] ?? []);
        $ytIds = preg_match_all('/data-yt-id="([^"]*)"/', $html, $ym) ? count(array_filter($ym[1])) : 0;
        $localSrcs = preg_match_all('/data-local-src="([^"]*)"/', $html, $lm) ? count(array_filter($lm[1])) : 0;
        $notEmpty = str_contains($html, 'Belum ada video') ? 'YES' : 'NO';

        $this->line('-- RENDER HALAMAN PUBLIK /video --');
        $this->line('HTML size: ' . strlen($html) . ' bytes');
        $this->line('Video trigger <button> count: ' . $triggers . ' (expect == ' . $rows->count() . ')');
        $this->line('Cards with youtube data: ' . $ytIds);
        $this->line('Cards with local src data: ' . $localSrcs);
        $this->line('"Belum ada video" shown: ' . $notEmpty . ' (expect NO)');

        foreach ($rows as $v) {
            $urlType = $v->isLocal() ? 'LOCAL' : 'YT';
            $this->line(sprintf('  -> #%d [%s] %s | embed=%s | thumb=%s', $v->id, $urlType, $v->title, $v->embed_url ?? 'null', $v->display_thumb ?? 'null'));
        }

        if ($triggers !== $rows->count()) {
            $this->error('MISMATCH: triggers in HTML (' . $triggers . ') != published rows (' . $rows->count() . ')');
        } else {
            $this->info('OK: jumlah <button> cocok dengan jumlah row published.');
        }
    }

    // Legacy method body di bawah (removed; see renderPublicPage)

    /**
     * Hasilkan byte minimal yang terdeteksi PHP sebagai MP4.
     * - Bytes 4-7 (atom size) cocok dengan header "ftyp".
     */
    private function makeDummyMp4Bytes(): string
    {
        // ftyp box: size 32 + 'ftyp' + 'isom'
        $ftyp = pack('N', 32) . 'ftyp' . 'isom' . str_repeat("\0", 24);
        // mdia/mdat: cukup banyak zero bytes agar tidak dianggap kosong
        $mdat = pack('N', 2048) . 'mdat' . str_repeat("\0", 2044);
        return $ftyp . $mdat;
    }

    private function makeDummyJpgBytes(): string
    {
        // JPG SOI marker + minimum content. Tidak harus bisa dibuka sebagai gambar,
        // untuk test ini hanya butuh ekstensi+MIME yang valid.
        return "\xFF\xD8\xFF\xE0" . pack('n', 16) . 'JFIF' . str_repeat("\0", 100);
    }
}
