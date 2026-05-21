<!-- resources/views/components/breadcrumb.blade.php -->
@php
    // Ensure $breadcrumbs is defined; fallback to empty array.
    $breadcrumbs = $breadcrumbs ?? [];
@endphp
@if (count($breadcrumbs) > 0)
<div class="breadcrumb anim-ready">
    @foreach ($breadcrumbs as $index => $crumb)
        @if (isset($crumb['url']) && $crumb['url'])
            <a href="{{ $crumb['url'] }}" class="breadcrumb-item">{{ $crumb['label'] }}</a>
        @else
            <span class="breadcrumb-item active">{{ $crumb['label'] }}</span>
        @endif
        @if (!$loop->last)
            <span class="breadcrumb-separator">/</span>
        @endif
    @endforeach
</div>
@endif
