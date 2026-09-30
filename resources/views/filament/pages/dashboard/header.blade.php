<header class="lg-dash-head">
    <div>
        <p class="lg-eyebrow">{{ $today }}</p>
        <h1 class="lg-dash-title">{{ $greeting }}{{ $name !== '' ? ', '.$name : '' }}</h1>
        <p class="lg-muted">Here's the state of the LITUS Group website today.</p>
    </div>

    <div class="lg-dash-head-actions">
        @if ($role)
            <span class="lg-badge lg-tone-info">{{ $role }}</span>
        @endif

        <a href="{{ url('/') }}" target="_blank" rel="noopener" class="lg-btn">
            <x-heroicon-o-external-link class="lg-icon-sm" />
            View website
        </a>
    </div>
</header>
