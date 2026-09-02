<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:4px;">
    <tr>
        <td style="width:110px; padding:0 12px 0 0; vertical-align:middle; text-align:center;">
            @if (!empty($logo))
                <img src="data:image/{{ $logo_type }};base64,{{ $logo }}" height="60"
                    style="display:block; margin:0 auto;">
            @endif
        </td>
        <td style="padding:0; vertical-align:middle; text-align:left;">
            <div style="font-weight:bold; font-size:14px; line-height:1.15; margin:0;">
                {{ strtoupper($profil->nama ?? 'SISTEM AKADEMIK') }}
            </div>
            <div style="font-size:9px; color:#555; line-height:1.15; margin:2px 0 0 0;">
                <i>{{ $profil->alamat ?? '-' }}</i>
            </div>
            @if (!empty($profil->telpon) || !empty($profil->email))
                <div style="font-size:9px; color:#555; line-height:1.15; margin:2px 0 0 0;">
                    @if (!empty($profil->telpon))Telp. {{ $profil->telpon }}@endif
                    @if (!empty($profil->telpon) && !empty($profil->email)) &middot; @endif
                    @if (!empty($profil->email))Email: {{ $profil->email }}@endif
                </div>
            @endif
        </td>
    </tr>
</table>

<div style="border-top: 2px solid #000; margin-top: 6px;"></div>
<div style="height:14px;"></div>
