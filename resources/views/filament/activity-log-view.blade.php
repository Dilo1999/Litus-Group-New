@php
    /** @var \App\Models\ActivityLog $record */
    $props = $record->properties;
    $summary = is_array($props) && array_key_exists('description', $props)
        ? (string) $props['description']
        : (string) ($record->description ?? '');
    $rest = is_array($props) ? $props : [];
    unset($rest['description']);
    $propertiesJson = $rest !== []
        ? json_encode($rest, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        : null;
@endphp

<div class="space-y-4 text-sm filament-body">
    <div>
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Date & time') }}</div>
        <div class="mt-0.5 text-gray-900 dark:text-gray-100">
            {{ $record->created_at?->format('M j, Y H:i:s') ?? '—' }}
        </div>
    </div>
    <div>
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('User (at time of action)') }}</div>
        <div class="mt-0.5 text-gray-900 dark:text-gray-100">{{ $record->actor_label }}</div>
    </div>
    <div>
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Event') }}</div>
        <div class="mt-0.5 text-gray-900 dark:text-gray-100">{{ $record->event ?? '—' }}</div>
    </div>
    <div>
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('What was done') }}</div>
        <div class="mt-0.5 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $summary !== '' ? $summary : '—' }}</div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Subject type') }}</div>
            <div class="mt-0.5 text-gray-900 dark:text-gray-100">
                {{ $record->subject_type ? class_basename($record->subject_type) : '—' }}
            </div>
        </div>
        <div>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Subject ID') }}</div>
            <div class="mt-0.5 text-gray-900 dark:text-gray-100">{{ $record->subject_id ?? '—' }}</div>
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('IP address') }}</div>
            <div class="mt-0.5 text-gray-900 dark:text-gray-100">{{ $record->ip_address ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Log name') }}</div>
            <div class="mt-0.5 text-gray-900 dark:text-gray-100">{{ $record->log_name ?? '—' }}</div>
        </div>
    </div>
    @if (filled($record->user_agent))
        <div>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('User agent') }}</div>
            <div class="mt-0.5 break-all text-gray-900 dark:text-gray-100">{{ $record->user_agent }}</div>
        </div>
    @endif
    @if ($propertiesJson !== null)
        <div>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Properties (JSON)') }}</div>
            <pre class="mt-1 max-h-96 overflow-auto rounded-lg bg-gray-50 p-3 text-xs text-gray-800 dark:bg-gray-800 dark:text-gray-100">{{ $propertiesJson }}</pre>
        </div>
    @endif
</div>
