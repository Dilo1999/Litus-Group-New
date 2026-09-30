@php
    use App\Filament\Resources\ActivityLogResource;
    use App\Filament\Resources\BlogPostResource;
    use App\Filament\Resources\JobOpeningResource;
    use Illuminate\Support\Str;
@endphp

<x-filament::page class="filament-dashboard-page lg-dash">
    {{-- Needs attention --}}
    @if ($attention !== [])
        <section class="lg-card lg-attention" aria-labelledby="lg-attention-title">
            <h2 id="lg-attention-title" class="lg-attention-title">
                <x-heroicon-o-exclamation class="lg-icon-sm" />
                Needs attention
            </h2>
            <ul class="lg-attention-list">
                @foreach ($attention as $item)
                    <li class="lg-attention-item">
                        <span class="lg-dot lg-tone-{{ $item['tone'] }}" aria-hidden="true"></span>
                        <span class="lg-attention-text">{{ $item['text'] }}</span>
                        <a href="{{ $item['url'] }}" class="lg-link">{{ $item['action'] }} <x-heroicon-o-arrow-right class="lg-icon-xs" /></a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- KPIs --}}
    @if ($stats !== [])
        <section class="lg-kpis" style="--lg-kpi-cols: {{ max(4, count($stats)) }}" data-count="{{ count($stats) }}" aria-label="Key figures">
            @foreach ($stats as $stat)
                <a href="{{ $stat['url'] }}" @class(['lg-card lg-kpi', 'lg-kpi-hero' => $stat['hero'] ?? false])>
                    <div class="lg-kpi-top">
                        <span class="lg-kpi-label">{{ $stat['label'] }}</span>
                        <span class="lg-kpi-icon"><x-dynamic-component :component="$stat['icon']" class="lg-icon" /></span>
                    </div>
                    <div class="lg-kpi-value">{{ number_format($stat['value']) }}</div>
                    <div class="lg-kpi-meta">{{ $stat['meta'] }}</div>
                </a>
            @endforeach
        </section>
    @endif

    @php
        $hasMain = $publishing || $recentPosts || $jobOpenings;
    @endphp

    <div @class(['lg-grid', 'lg-grid-single' => ! $hasMain])>
        @if ($hasMain)
        <div class="lg-col-main">
            {{-- Publishing trend --}}
            @if ($publishing)
                <section class="lg-card">
                    <div class="lg-card-head">
                        <div>
                            <h2 class="lg-card-title">Publishing activity</h2>
                            <p class="lg-muted">Blog posts published per month · last 6 months</p>
                        </div>
                        <div class="lg-card-figure">
                            <span class="lg-card-figure-value">{{ $publishing['total'] }}</span>
                            <span class="lg-muted">{{ Str::plural('post', $publishing['total']) }}</span>
                        </div>
                    </div>

                    <div class="lg-bars" role="img" aria-label="Posts per month: {{ collect($publishing['months'])->map(fn ($m) => $m['full'].' '.$m['count'])->join(', ') }}">
                        <div class="lg-bars-axis" aria-hidden="true">
                            @foreach ([1, 0.5, 0] as $t)
                                <span>{{ (int) round($publishing['max'] * $t) }}</span>
                            @endforeach
                        </div>
                        <div class="lg-bars-plot" aria-hidden="true">
                            @foreach ($publishing['months'] as $m)
                                <div class="lg-bar-col" title="{{ $m['full'] }}: {{ $m['count'] }} {{ Str::plural('post', $m['count']) }}">
                                    <div class="lg-bar-track">
                                        <div @class(['lg-bar', 'lg-bar-current' => $m['current']]) style="height: {{ $m['count'] ? max(4, $m['count'] / $publishing['max'] * 100) : 0 }}%">
                                            @if ($m['count'])
                                                <span class="lg-bar-value">{{ $m['count'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="lg-bar-label">{{ $m['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            {{-- Recent posts --}}
            @if ($recentPosts)
                <section class="lg-card lg-card-flush">
                    <div class="lg-card-head lg-pad">
                        <div>
                            <h2 class="lg-card-title">Recently updated posts</h2>
                            <p class="lg-muted">News &amp; Media</p>
                        </div>
                        <a href="{{ BlogPostResource::getUrl('index') }}" class="lg-link">View all <x-heroicon-o-arrow-right class="lg-icon-xs" /></a>
                    </div>

                    @if ($recentPosts->isEmpty())
                        <div class="lg-empty">
                            <span class="lg-kpi-icon"><x-heroicon-o-newspaper class="lg-icon" /></span>
                            <p class="lg-strong">No blog posts yet</p>
                            <p class="lg-muted">Publish your first story to populate News &amp; Media.</p>
                            @if (BlogPostResource::canCreate())
                                <a href="{{ BlogPostResource::getUrl('create') }}" class="lg-btn lg-btn-primary">Create post</a>
                            @endif
                        </div>
                    @else
                        <div class="lg-table-wrap">
                            <table class="lg-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Title</th>
                                        <th scope="col">Status</th>
                                        <th scope="col" class="lg-r lg-hide-sm">Date</th>
                                        <th scope="col"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentPosts as $post)
                                        <tr>
                                            <td>
                                                <a href="{{ $post['url'] }}" class="lg-strong lg-row-link">{{ Str::limit($post['title'], 70) }}</a>
                                                @if ($post['category'])
                                                    <div class="lg-muted lg-small">{{ $post['category'] }}</div>
                                                @endif
                                            </td>
                                            <td><span class="lg-badge lg-tone-{{ $post['status']['tone'] }}">{{ $post['status']['label'] }}</span></td>
                                            <td class="lg-r lg-num lg-muted lg-hide-sm">{{ $post['date'] }}</td>
                                            <td class="lg-r">
                                                <a href="{{ $post['url'] }}" class="lg-icon-btn" aria-label="Edit {{ $post['title'] }}"><x-heroicon-o-pencil class="lg-icon-sm" /></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            @endif

            {{-- Job openings --}}
            @if ($jobOpenings)
                <section class="lg-card lg-card-flush">
                    <div class="lg-card-head lg-pad">
                        <div>
                            <h2 class="lg-card-title">Job openings</h2>
                            <p class="lg-muted">Listed on the Careers page</p>
                        </div>
                        <a href="{{ JobOpeningResource::getUrl('index') }}" class="lg-link">Manage <x-heroicon-o-arrow-right class="lg-icon-xs" /></a>
                    </div>

                    @if ($jobOpenings->isEmpty())
                        <div class="lg-empty">
                            <span class="lg-kpi-icon"><x-heroicon-o-briefcase class="lg-icon" /></span>
                            <p class="lg-strong">No job openings yet</p>
                            <p class="lg-muted">Add a role to start receiving applications through Careers.</p>
                            @if (JobOpeningResource::canCreate())
                                <a href="{{ JobOpeningResource::getUrl('create') }}" class="lg-btn lg-btn-primary">Add opening</a>
                            @endif
                        </div>
                    @else
                        <div class="lg-table-wrap">
                            <table class="lg-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Position</th>
                                        <th scope="col" class="lg-hide-sm">Type</th>
                                        <th scope="col">Status</th>
                                        <th scope="col"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jobOpenings as $job)
                                        <tr>
                                            <td>
                                                <a href="{{ $job['url'] }}" class="lg-strong lg-row-link">{{ $job['title'] }}</a>
                                                @if ($job['meta'])
                                                    <div class="lg-muted lg-small">{{ $job['meta'] }}</div>
                                                @endif
                                            </td>
                                            <td class="lg-muted lg-hide-sm">{{ $job['type'] ?: '—' }}</td>
                                            <td><span class="lg-badge lg-tone-{{ $job['status']['tone'] }}">{{ $job['status']['label'] }}</span></td>
                                            <td class="lg-r">
                                                <a href="{{ $job['url'] }}" class="lg-icon-btn" aria-label="Edit {{ $job['title'] }}"><x-heroicon-o-pencil class="lg-icon-sm" /></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            @endif
        </div>
        @endif

        <aside class="lg-col-side">
            @if ($attention === [])
                <section class="lg-card lg-allclear">
                    <span class="lg-feed-icon lg-tone-success"><x-heroicon-o-check-circle class="lg-icon-sm" /></span>
                    <div>
                        <p class="lg-strong">All clear</p>
                        <p class="lg-muted lg-small">No drafts, gaps or missing SEO to fix right now.</p>
                    </div>
                </section>
            @endif

            {{-- Quick actions --}}
            @if ($quickActions !== [])
                <section class="lg-card">
                    <h2 class="lg-card-title lg-mb">Quick actions</h2>
                    <div class="lg-actions">
                        @foreach ($quickActions as $action)
                            <a href="{{ $action['url'] }}" class="lg-action">
                                <span class="lg-kpi-icon"><x-dynamic-component :component="$action['icon']" class="lg-icon" /></span>
                                <span>{{ $action['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Activity --}}
            @if ($activity)
                <section class="lg-card">
                    <div class="lg-card-head">
                        <h2 class="lg-card-title">Recent activity</h2>
                        <a href="{{ ActivityLogResource::getUrl('index') }}" class="lg-link">Full log <x-heroicon-o-arrow-right class="lg-icon-xs" /></a>
                    </div>

                    @if ($activity->isEmpty())
                        <p class="lg-muted">No activity recorded yet.</p>
                    @else
                        <ol class="lg-feed">
                            @foreach ($activity as $entry)
                                <li class="lg-feed-item">
                                    <span class="lg-feed-icon lg-tone-{{ $entry['tone'] }}"><x-dynamic-component :component="$entry['icon']" class="lg-icon-sm" /></span>
                                    <div class="lg-feed-body">
                                        <p><span class="lg-strong">{{ $entry['actor'] }}</span> <span class="lg-muted">·</span> {{ Str::limit($entry['description'], 80) }}</p>
                                        <p class="lg-muted lg-small">{{ $entry['time'] }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </section>
            @endif
        </aside>
    </div>
</x-filament::page>
