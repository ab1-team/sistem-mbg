@props([
    'title'   => '',
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-[20px] border border-slate-100']) }}>
    @if($title)
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-50">
            <h3 class="text-[14px] font-bold text-slate-900">{{ $title }}</h3>
            @isset($action)
                <div>{{ $action }}</div>
            @endisset
        </div>
    @endif

    <div @class(['p-6' => $padding])>
        {{ $slot }}
    </div>
</div>
