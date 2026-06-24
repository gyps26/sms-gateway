<x-mail::message>
# {{ __('emails.hi', ['name' => $subscription->user->name]) }}

{{ __('emails.subscriptions.expired.message') }}

<x-mail::button :url="route('subscriptions.index', ['search' => $subscription->subscription_id])">
    {{ __('emails.subscriptions.view') }}
</x-mail::button>

{{ __('emails.subscriptions.contact') }}<br>

<x-mail::button :url="config('app.support_url')">
    {{ __('emails.subscriptions.need_help') }}
</x-mail::button>

{{ __('emails.best_regards') }}<br>
{{ config('app.name') }}
</x-mail::message>
