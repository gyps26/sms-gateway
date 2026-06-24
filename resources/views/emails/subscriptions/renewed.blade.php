<x-mail::message>
# {{ __('emails.hi', ['name' => $subscription->user->name]) }}

{{ __('emails.subscriptions.renewed.message') }}

@if($subscription->renewal_at)
{{ __('emails.subscriptions.next_renewal', ['date' => $subscription->renewal_at->toFormattedDateString()]) }}
@elseif($subscription->ends_at)
{{ __('emails.subscriptions.ends', ['date' => $subscription->ends_at->toFormattedDateString()]) }}
@endif

{{ __('emails.subscriptions.renewed.features') }}

<x-mail::button :url="route('subscriptions.index', ['search' => $subscription->subscription_id])">
{{ __('emails.subscriptions.view') }}
</x-mail::button>

{{ __('emails.subscriptions.contact') }}

<x-mail::button :url="config('app.support_url')">
    {{ __('emails.subscriptions.need_help') }}
</x-mail::button>

{{ __('emails.best_regards') }}<br>
{{ config('app.name') }}
</x-mail::message>