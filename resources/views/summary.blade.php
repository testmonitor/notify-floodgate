@component('mail::message')
@if ($summary->title)
# {{ __($summary->title, $summary->data) }}
@endif

{{ __($summary->message, $summary->data) }}

@component('mail::table')
| # | @lang('Notification') |
| - | ----------- |
@foreach ($items as $index => $item)
| {{ $index + 1 }} | [{{ __($item['message'] ?? '', $item['data'] ?? []) }}]({{ $item['url'] ?? '#' }}) |
@endforeach
@endcomponent

@if ($summary->actionText)
@component('mail::button', ['url' => $summary->actionUrl])
@lang($summary->actionText)
@endcomponent
@endif

@endcomponent
