<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ActivityLogResource;
use App\Filament\Resources\BlogPostResource;
use App\Filament\Resources\CompanyResource;
use App\Filament\Resources\GalleryEventResource;
use App\Filament\Resources\JobOpeningResource;
use App\Filament\Resources\TeamMemberResource;
use App\Filament\Resources\UserResource;
use App\Models\ActivityLog;
use App\Models\BlogPost;
use App\Models\Company;
use App\Models\GalleryEvent;
use App\Models\JobOpening;
use App\Models\TeamMember;
use App\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Dashboard extends BaseDashboard
{
    protected static string $view = 'filament.pages.dashboard';

    protected function getHeader(): ?View
    {
        $user = auth()->user();
        $hour = (int) now()->format('G');

        return view('filament.pages.dashboard.header', [
            'greeting' => $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening'),
            'name' => $user?->name ?? '',
            'role' => User::roleOptions()[$user?->role] ?? null,
            'today' => now()->format('l, j F Y'),
        ]);
    }

    protected function getWidgets(): array
    {
        return [];
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->getStats(),
            'attention' => $this->getAttentionItems(),
            'publishing' => BlogPostResource::canViewAny() ? $this->getPublishingTrend() : null,
            'recentPosts' => BlogPostResource::canViewAny() ? $this->getRecentPosts() : null,
            'jobOpenings' => JobOpeningResource::canViewAny() ? $this->getJobOpenings() : null,
            'activity' => ActivityLogResource::canViewAny() ? $this->getRecentActivity() : null,
            'quickActions' => $this->getQuickActions(),
        ];
    }

    /**
     * KPI cards — one per resource the current user may open.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getStats(): array
    {
        $stats = [];

        if (JobOpeningResource::canViewAny()) {
            $open = JobOpening::where('is_active', true)->count();
            $stats[] = [
                'label' => 'Open positions',
                'value' => $open,
                'meta' => $this->plural(JobOpening::where('is_active', true)->distinct()->count('company'), 'company', 'companies').' hiring',
                'icon' => 'heroicon-o-briefcase',
                'url' => JobOpeningResource::getUrl('index'),
            ];
        }

        if (BlogPostResource::canViewAny()) {
            $stats[] = [
                'label' => 'Published posts',
                'value' => BlogPost::where('is_active', true)->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))->count(),
                'meta' => BlogPost::where('is_active', false)->count().' drafts',
                'icon' => 'heroicon-o-newspaper',
                'url' => BlogPostResource::getUrl('index'),
            ];
        }

        if (GalleryEventResource::canViewAny()) {
            $events = GalleryEvent::where('is_active', true)->get(['gallery_images']);
            $stats[] = [
                'label' => 'Gallery events',
                'value' => $events->count(),
                'meta' => $this->plural($events->sum(fn ($e) => count($e->gallery_images ?? [])), 'photo').' published',
                'icon' => 'heroicon-o-photograph',
                'url' => GalleryEventResource::getUrl('index'),
            ];
        }

        if (CompanyResource::canViewAny()) {
            $stats[] = [
                'label' => 'Companies',
                'value' => Company::count(),
                'meta' => Company::where('featured', true)->count().' featured on home',
                'icon' => 'heroicon-o-office-building',
                'url' => CompanyResource::getUrl('index'),
            ];
        }

        if (TeamMemberResource::canViewAny()) {
            $stats[] = [
                'label' => 'Team members',
                'value' => TeamMember::where('is_active', true)->count(),
                'meta' => 'shown on Team page',
                'icon' => 'heroicon-o-user-group',
                'url' => TeamMemberResource::getUrl('index'),
            ];
        }

        if (UserResource::canViewAny()) {
            $stats[] = [
                'label' => 'Admin users',
                'value' => User::visibleTo(auth()->user())->count(),
                'meta' => 'with panel access',
                'icon' => 'heroicon-o-users',
                'url' => UserResource::getUrl('index'),
            ];
        }

        // Highlight the first metric the user is responsible for.
        if ($stats !== []) {
            $stats[0]['hero'] = true;
        }

        return array_slice($stats, 0, 6);
    }

    /**
     * Actionable content gaps, derived from real records.
     *
     * @return array<int, array<string, string>>
     */
    protected function getAttentionItems(): array
    {
        $items = [];

        if (BlogPostResource::canViewAny()) {
            if ($drafts = BlogPost::where('is_active', false)->count()) {
                $items[] = [
                    'tone' => 'warning',
                    'text' => $this->plural($drafts, 'blog post').' saved as draft and hidden from News & Media',
                    'action' => 'Review drafts',
                    'url' => BlogPostResource::getUrl('index'),
                ];
            }

            if ($scheduled = BlogPost::where('is_active', true)->where('published_at', '>', now())->count()) {
                $items[] = [
                    'tone' => 'info',
                    'text' => $this->plural($scheduled, 'post').' scheduled to publish later',
                    'action' => 'View',
                    'url' => BlogPostResource::getUrl('index'),
                ];
            }
        }

        if (JobOpeningResource::canViewAny() && ! JobOpening::where('is_active', true)->exists()) {
            $items[] = [
                'tone' => 'warning',
                'text' => 'No active job openings — the Careers page shows an empty list',
                'action' => 'Add opening',
                'url' => JobOpeningResource::getUrl('create'),
            ];
        }

        if (CompanyResource::canViewAny() && ($missing = Company::where(fn ($q) => $q->whereNull('meta_description')->orWhere('meta_description', ''))->count())) {
            $items[] = [
                'tone' => 'neutral',
                'text' => $this->plural($missing, 'company page').' missing an SEO meta description',
                'action' => 'Fix SEO',
                'url' => CompanyResource::getUrl('index'),
            ];
        }

        if (TeamMemberResource::canViewAny() && ($noPhoto = TeamMember::where('is_active', true)->where(fn ($q) => $q->whereNull('photo')->orWhere('photo', ''))->count())) {
            $items[] = [
                'tone' => 'neutral',
                'text' => $this->plural($noPhoto, 'team member').' without a profile photo',
                'action' => 'Add photos',
                'url' => TeamMemberResource::getUrl('index'),
            ];
        }

        return array_slice($items, 0, 4);
    }

    /**
     * Posts published per month over the last 6 months.
     *
     * @return array{months: array<int, array{label: string, count: int, current: bool}>, max: int, total: int}
     */
    protected function getPublishingTrend(): array
    {
        $start = now()->startOfMonth()->subMonths(5);

        $counts = BlogPost::where('is_active', true)
            ->whereBetween('published_at', [$start, now()])
            ->pluck('published_at')
            ->countBy(fn (Carbon $date) => $date->format('Y-m'));

        $months = collect(range(0, 5))->map(function (int $offset) use ($start, $counts) {
            $month = $start->copy()->addMonths($offset);

            return [
                'label' => $month->format('M'),
                'full' => $month->format('F Y'),
                'count' => (int) ($counts[$month->format('Y-m')] ?? 0),
                'current' => $offset === 5,
            ];
        });

        $max = max(1, $months->max('count'));

        return [
            'months' => $months->all(),
            // Round the axis top to a friendly number.
            'max' => $max <= 4 ? 4 : (int) (ceil($max / 5) * 5),
            'total' => $months->sum('count'),
        ];
    }

    protected function getRecentPosts()
    {
        return BlogPost::query()
            ->latest('updated_at')
            ->limit(5)
            ->get(['id', 'title', 'category', 'is_active', 'published_at', 'updated_at'])
            ->map(fn (BlogPost $post) => [
                'title' => $post->title,
                'category' => $post->category,
                'status' => match (true) {
                    ! $post->is_active => ['label' => 'Draft', 'tone' => 'neutral'],
                    $post->published_at?->isFuture() => ['label' => 'Scheduled', 'tone' => 'info'],
                    default => ['label' => 'Published', 'tone' => 'success'],
                },
                'date' => ($post->published_at ?? $post->updated_at)?->format('j M Y'),
                'url' => BlogPostResource::getUrl('edit', ['record' => $post]),
            ]);
    }

    protected function getJobOpenings()
    {
        return JobOpening::query()
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->limit(6)
            ->get(['id', 'title', 'company', 'location', 'type', 'is_active'])
            ->map(fn (JobOpening $job) => [
                'title' => $job->title,
                'meta' => collect([$job->company, $job->location])->filter()->join(' · '),
                'type' => $job->type,
                'status' => $job->is_active
                    ? ['label' => 'Open', 'tone' => 'success']
                    : ['label' => 'Hidden', 'tone' => 'neutral'],
                'url' => JobOpeningResource::getUrl('edit', ['record' => $job]),
            ]);
    }

    protected function getRecentActivity()
    {
        return ActivityLog::query()
            ->latest('created_at')
            ->limit(6)
            ->get()
            ->map(fn (ActivityLog $log) => [
                'actor' => $log->actor_name ?: 'System',
                'description' => $log->description ?: Str::headline($log->event).' '.class_basename((string) $log->subject_type),
                'tone' => match ($log->event) {
                    'created' => 'success',
                    'deleted' => 'danger',
                    'login' => 'info',
                    default => 'neutral',
                },
                'icon' => match ($log->event) {
                    'created' => 'heroicon-o-plus',
                    'deleted' => 'heroicon-o-trash',
                    'login' => 'heroicon-o-login',
                    default => 'heroicon-o-pencil',
                },
                'time' => $log->created_at?->diffForHumans(),
            ]);
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function getQuickActions(): array
    {
        return collect([
            [BlogPostResource::class, 'New blog post', 'heroicon-o-newspaper'],
            [GalleryEventResource::class, 'New gallery event', 'heroicon-o-photograph'],
            [JobOpeningResource::class, 'New job opening', 'heroicon-o-briefcase'],
            [TeamMemberResource::class, 'New team member', 'heroicon-o-user-add'],
        ])
            ->filter(fn (array $a) => $a[0]::canViewAny() && $a[0]::canCreate())
            ->map(fn (array $a) => ['label' => $a[1], 'icon' => $a[2], 'url' => $a[0]::getUrl('create')])
            ->values()
            ->all();
    }

    private function plural(int $count, string $singular, ?string $plural = null): string
    {
        return number_format($count).' '.($count === 1 ? $singular : ($plural ?? Str::plural($singular)));
    }
}
