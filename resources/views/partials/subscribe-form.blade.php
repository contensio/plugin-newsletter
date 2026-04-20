@php
    use Contensio\Newsletter\Support\NewsletterConfig;
    $nlConfig = NewsletterConfig::all();
@endphp

<div class="newsletter-subscribe-form">
    @if(session('newsletter_success'))
    <div class="rounded-xl bg-green-50 border border-green-200 px-5 py-4 text-green-800 font-medium text-base">
        {{ session('newsletter_success') }}
    </div>
    @else

    @if($nlConfig['form_title'])
    <p class="font-display text-2xl font-semibold text-ink-900 mb-2">{{ $nlConfig['form_title'] }}</p>
    @endif

    @if($nlConfig['form_description'])
    <p class="text-ink-500 text-lg mb-5">{{ $nlConfig['form_description'] }}</p>
    @endif

    <form method="POST" action="{{ route('contensio-newsletter.subscribe') }}" class="flex flex-col sm:flex-row gap-3">
        @csrf
        <input type="email" name="email" required
               placeholder="{{ $nlConfig['form_placeholder'] }}"
               class="flex-1 rounded-xl border border-cream-300 bg-white px-4 py-3 text-base text-ink-900 placeholder-ink-400 focus:outline-none focus:ring-2 focus:ring-ember-400">
        <button type="submit"
                class="shrink-0 bg-ember-500 hover:bg-ember-600 text-white font-semibold text-base px-6 py-3 rounded-xl transition-colors">
            {{ $nlConfig['form_button'] }}
        </button>
    </form>

    @if($errors->has('email'))
    <p class="mt-2 text-red-600 text-sm">{{ $errors->first('email') }}</p>
    @endif

    @endif
</div>
