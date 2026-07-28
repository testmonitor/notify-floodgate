@component('mail::message')
@if ($summary->title)
# {{ $summary->title }}
@endif

{{ $summary->message }}

@component('mail::table')
| # | @lang('Notification') |
| - | ----------- |
@foreach ($items as $index => $item)
| {{ $index + 1 }} | [{{ __($item['message'] ?? '', $item['data'] ?? []) }}]({{ $item['url'] ?? '#' }}) |
@endforeach
@endcomponent

@if ($summary->actionText)
@component('mail::button', ['url' => $summary->actionUrl])
{{ $summary->actionText }}
@endcomponent
@endif

@endcomponent
