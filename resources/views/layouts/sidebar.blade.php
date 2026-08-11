@php
    use Illuminate\Support\Facades\DB;
    $hakAkses = auth()->check() ? (auth()->user()->hak_akses ?? []) : [];
    $hakAkses = is_array($hakAkses) ? $hakAkses : [];

    $hasAllAccess = in_array('*', $hakAkses, true);

    $menus = collect();
    if ($hasAllAccess || !empty($hakAkses)) {
        $parentQuery = DB::table('menu')
            ->where('status', 'aktif')
            ->orderBy('urutan');

        if (!$hasAllAccess) {
            $parentQuery->whereIn('id', $hakAkses);
        }

        $parents = $parentQuery->get();

        $childIds = $parents->whereNotNull('parent_id')->pluck('id')->all();
        $childExistingParents = $parents->whereNull('parent_id')->pluck('id')->all();
        $childIds = array_merge($childIds, DB::table('menu')
            ->whereIn('parent_id', $childExistingParents)
            ->where('status', 'aktif')
            ->pluck('id')->all());

        $children = collect();
        if (!empty($childIds)) {
            $children = DB::table('menu')
                ->whereIn('id', array_unique($childIds))
                ->where('status', 'aktif')
                ->orderBy('urutan')
                ->get();
        }

        $menus = $parents->whereNull('parent_id')->merge($children)->values();
    } elseif (auth()->check()) {
        $menus = DB::table('menu')
            ->where('status', 'aktif')
            ->orderBy('urutan')
            ->get();
    }

    $beranda = $menus->firstWhere('nama_menu', 'Beranda');

    $currentPath = trim(request()->path(), '/');
    $isActive = function ($route) use ($currentPath) {
        if (empty($route) || $route === '#') return false;
        $route = trim($route, '/');
        if ($route === '' || $route === '/') return $currentPath === '';
        return $currentPath === $route;
    };

    $safeUrl = function ($route) {
        return !empty($route) ? url($route) : 'javascript:void(0)';
    };

    $groupOrder = [
        'Pengaturan' => 'submenusettigs',
        'Master Data' => null,
        'Transaksi' => null,
        'Pelaporan' => null,
    ];

    // Kelompokkan parent menu berdasarkan group. Parent akan di-render di
    // section group-nya; child yang menempel pada parent diambil via
    // $menus->where('parent_id', ...).
    $parentsByGroup = $menus->whereNull('parent_id')->groupBy('group');
@endphp

<div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main" style="height: calc(100vh - 120px); overflow-y: auto;">
    <ul class="navbar-nav">
        @if($beranda)
            <li class="nav-item">
                <a class="nav-link {{ $isActive($beranda->route) ? 'active nav-active' : 'text-dark' }}" href="{{ $safeUrl($beranda->route) }}">
                    <span class="material-symbols-rounded opacity-5">{{ $beranda->icon }}</span>
                    <span class="nav-link-text ms-1">{{ $beranda->nama_menu }}</span>
                </a>
            </li>
        @endif

        @foreach($groupOrder as $groupName => $fixedCollapseId)
            @php
                $items = $parentsByGroup->get($groupName, collect());
            @endphp

            @if($items->isNotEmpty())
                <p class="text-uppercase text-muted ms-3 mb-1 mt-3" style="font-size: 12px;">
                    {{ $groupName }}
                </p>

                @foreach($items as $item)
                    @php
                        $itemChildren = $menus->where('parent_id', $item->id)->sortBy('urutan')->values();
                        $isDropdown = $itemChildren->isNotEmpty();
                        $collapseId = $fixedCollapseId ?? ('submenu_' . $item->id);
                    @endphp

                    @if($isDropdown)
                        @php
                            $childActive = $itemChildren->contains(fn($c) => $isActive($c->route));
                            $parentActive = $isActive($item->route);
                            $showOpen = $childActive || $parentActive;
                        @endphp
                        <li class="nav-item">
                            <a data-bs-toggle="collapse" href="#{{ $collapseId }}" class="nav-link {{ $showOpen ? 'active nav-active' : 'text-dark' }} py-2 my-1"
                                aria-controls="{{ $collapseId }}" role="button" aria-expanded="{{ $showOpen ? 'true' : 'false' }}">
                                @if($item->icon)
                                    <span class="material-symbols-rounded opacity-5">{{ $item->icon }}</span>
                                @endif
                                <span class="sidenav-normal ms-1">{{ $item->nama_menu }}</span>
                            </a>
                            <div class="collapse {{ $showOpen ? 'show' : '' }}" id="{{ $collapseId }}">
                                <ul class="nav ms-4">
                                    @foreach($itemChildren as $child)
                                        @php $childIsActive = $isActive($child->route); @endphp
                                        <li class="nav-item">
                                            <a class="nav-link text-dark py-2" href="{{ $safeUrl($child->route) }}">
                                                <span class="sidenav-normal">{{ $childIsActive ? '• ' : '' }}{{ $child->nama_menu }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @else
                        @php $itemActive = $isActive($item->route); @endphp
                        <li class="nav-item">
                            <a class="nav-link {{ $itemActive ? 'active nav-active' : 'text-dark' }} py-2 my-1" href="{{ $safeUrl($item->route) }}">
                                @if($item->icon)
                                    <span class="material-symbols-rounded opacity-5">{{ $item->icon }}</span>
                                @endif
                                <span class="sidenav-normal ms-1">{{ $item->nama_menu }}</span>
                            </a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach
    </ul>
</div>