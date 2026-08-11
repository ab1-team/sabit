<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:4px;">
    <tr>
        <td style="width:110px; padding:0 12px 0 0; vertical-align:middle; text-align:center;">
            @if (!empty($logo))
                <img src="data:image/{{ $logo_type }};base64,{{ $logo }}" height="80"
                    style="display:block; margin:0 auto;">
            @endif
        </td>
        <td style="padding:0; vertical-align:middle; text-align:center;">
            <div style="font-weight:bold; font-size:18px; line-height:1.15; margin:0;">
                {{ strtoupper($profil->nama ?? 'SISTEM AKADEMIK') }}
            </div>
            <div style="font-weight:bold; font-size:14px; line-height:1.15; margin:2px 0 0 0;">
                PEMBAYARAN SPP
            </div>
            <div style="font-size:10px; color:#555; line-height:1.15; margin:2px 0 0 0;">
                <i>{{ $profil->alamat ?? 'alamat' }}</i>
            </div>
        </td>
    </tr>
</table>

<div style="border-top: 2px solid #000; margin-top: 6px;"></div>
<div style="height:14px;"></div>
