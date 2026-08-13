{{--
    Partial recursive untuk render menu children di halaman Hak Akses pusat.
    Dirancang agar support multi-level (PPDB > Pengaturan PPDB, dst).

    Variabel yang diharapkan:
      - $children: Collection<menu>
      - $bucket:   array dengan key 'children' (Collection<parent_id, Collection<menu>>)
      - $selected: array<int> id menu yang sudah tercentang untuk user saat ini
--}}
@foreach ($children as $child)
    @php
        $subChildren = $bucket['children']->get($child->id, collect());
        $hasSub = $subChildren->isNotEmpty();
    @endphp
    <div>
        <label class="menu-row flex items-center gap-2.5 rounded-md px-2 py-1 cursor-pointer">
            <input type="checkbox" class="cb menu-cb child-cb h-3.5 w-3.5 flex-shrink-0" value="{{ $child->id }}" @checked(in_array($child->id, $selected))>
            <span class="text-xs text-slate-600">{{ $child->nama_menu }}</span>
        </label>
        @if ($hasSub)
            <div class="ml-5 mt-0.5 space-y-0.5 border-l-2 border-slate-100 pl-3">
                @include('tenant.hak-akses._menu-children', [
                    'children' => $subChildren,
                    'bucket' => $bucket,
                    'selected' => $selected,
                ])
            </div>
        @endif
    </div>
@endforeach