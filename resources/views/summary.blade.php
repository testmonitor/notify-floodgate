@component('mail::message')
# {{ __($summary['message'] ?? 'You have new notifications', $summary['properties'] ?? []) }}

@component('mail::table')
| # | Notification |
| - | ----------- |
@foreach ($items as $index => $item)
| {{ $index + 1 }} | [{{ __($item['message'] ?? '', $item['properties'] ?? []) }}]({{ $item['url'] ?? '#' }}) |
@endforeach
@endcomponent

@component('mail::button', ['url' => $summary['url'] ?? '/'])
View
@endcomponent

@endcomponent
