<?php

namespace App\Models;

use App\Utils\Tanggal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TandaTangan extends Model
{
    use HasFactory;
    protected $table = 'tanda_tangan';
    protected $guarded = ['id'];

    protected $casts = [
        'tanda_tangan' => 'string',
    ];

    public function applyDatePlaceholders(?string $tgl = null): self
    {
        if (empty($this->tanda_tangan)) {
            return $this;
        }

        $tgl = $tgl ?: date('Y-m-d');
        $labelTanggal = Tanggal::tglLatin($tgl);

        $this->tanda_tangan = str_replace('{tanggal}', $labelTanggal, $this->tanda_tangan);

        return $this;
    }
}
