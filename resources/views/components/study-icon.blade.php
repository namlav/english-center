@props(['name'])
<svg {{ $attributes->merge(['class' => 'icon']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@switch($name)
    @case('book') <path d="M3 4h6a4 4 0 0 1 3 1.3A4 4 0 0 1 15 4h6v16h-6a4 4 0 0 0-3 1 4 4 0 0 0-3-1H3zM12 5v16M6 8h3M15 8h3M6 12h3M15 12h3"/> @break
    @case('arrow') <path d="M4 12h16m-6-6 6 6-6 6"/> @break
    @case('search') <circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/> @break
    @case('clock') <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/> @break
    @case('file') <path d="M14 3H5v18h14V8zM14 3v5h5M8 12h8M8 16h6"/> @break
    @case('check') <path d="m5 12 4 4L19 6"/> @break
    @case('target') <circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/> @break
    @case('spark') <path d="m12 3 2.5 6.5L21 12l-6.5 2.5L12 21l-2.5-6.5L3 12l6.5-2.5z"/> @break
    @case('layers') <path d="m12 3 10 5-10 5L2 8zM2 12l10 5 10-5M2 16l10 5 10-5"/> @break
    @case('menu') <path d="M4 6h16M4 12h16M4 18h16"/> @break
    @case('user') <circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/> @break
    @case('leaf') <path d="M20 3C5 1 1 11 8 16s14 0 12-13ZM5 21 16 9"/> @break
    @case('cart') <path d="M3 3h2l3 12h10l3-9H6M9 15l-1 3h11"/><circle cx="9" cy="21" r="1"/><circle cx="18" cy="21" r="1"/> @break
    @case('trash') <path d="M4 6h16M9 6V3h6v3M6 6l1 15h10l1-15M10 10v7M14 10v7"/> @break
    @case('qr') <path d="M3 3h6v6H3zM15 3h6v6h-6zM3 15h6v6H3zM15 15h3v3h3v3h-6zM12 3v3M12 9v3h6M3 12h6M12 15v6M21 12v3"/> @break
    @case('download') <path d="M12 3v12m-4-4 4 4 4-4M4 17v4h16v-4"/> @break
@endswitch
</svg>

