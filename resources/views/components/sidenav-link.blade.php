@props(['route', 'label', 'icon'])

@php $active = request()->fullUrlIs($route.'*'); @endphp

<a href="{{ $route }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ $active ? 'bg-[#FFC300] text-[#001D3D]' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
    <i class="{{ $icon }} text-lg"></i>
    <span>{{ $label }}</span>
</a>