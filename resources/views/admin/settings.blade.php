@extends('contensio::admin.layout')

@section('title', 'Newsletter Settings')

@section('content')
<div class="p-6 max-w-2xl">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('newsletter.subscribers') }}"
           class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="bi bi-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Newsletter Settings</h1>
            <p class="mt-0.5 text-gray-500">Configure subscription behaviour and the signup form.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-800 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('newsletter.settings.save') }}" class="space-y-6">
        @csrf

        {{-- Double opt-in --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="double_optin" value="1"
                       class="mt-0.5 w-5 h-5 rounded border-gray-300 text-ember-500 focus:ring-ember-400"
                       {{ old('double_optin', $config['double_optin']) ? 'checked' : '' }}>
                <div>
                    <span class="font-semibold text-gray-900">Double opt-in</span>
                    <p class="text-sm text-gray-500 mt-0.5">
                        When enabled, subscribers receive a confirmation email and are only added as active after clicking the link.
                        Recommended — reduces spam and fake signups.
                    </p>
                </div>
            </label>
        </div>

        {{-- Sender --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
            <h2 class="text-base font-semibold text-gray-900">Confirmation email sender</h2>
            <p class="text-sm text-gray-500 -mt-3">Used for the double opt-in confirmation email. Leave blank to use your site's default mail settings.</p>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="from_name" class="block text-sm font-medium text-gray-700 mb-1">From name</label>
                    <input id="from_name" name="from_name" type="text"
                           value="{{ old('from_name', $config['from_name']) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ember-400"
                           maxlength="100" placeholder="{{ config('app.name') }}">
                </div>
                <div>
                    <label for="from_email" class="block text-sm font-medium text-gray-700 mb-1">From email</label>
                    <input id="from_email" name="from_email" type="email"
                           value="{{ old('from_email', $config['from_email']) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ember-400"
                           maxlength="255" placeholder="{{ config('mail.from.address') }}">
                </div>
            </div>
        </div>

        {{-- Form copy --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
            <h2 class="text-base font-semibold text-gray-900">Signup form copy</h2>

            <div>
                <label for="form_title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input id="form_title" name="form_title" type="text"
                       value="{{ old('form_title', $config['form_title']) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ember-400"
                       maxlength="120" required>
            </div>

            <div>
                <label for="form_description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400 font-normal">(optional)</span></label>
                <textarea id="form_description" name="form_description" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ember-400 resize-none"
                          maxlength="300">{{ old('form_description', $config['form_description']) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="form_placeholder" class="block text-sm font-medium text-gray-700 mb-1">Email placeholder</label>
                    <input id="form_placeholder" name="form_placeholder" type="text"
                           value="{{ old('form_placeholder', $config['form_placeholder']) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ember-400"
                           maxlength="80" required>
                </div>
                <div>
                    <label for="form_button" class="block text-sm font-medium text-gray-700 mb-1">Button label</label>
                    <input id="form_button" name="form_button" type="text"
                           value="{{ old('form_button', $config['form_button']) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ember-400"
                           maxlength="60" required>
                </div>
            </div>
        </div>

        {{-- Success messages --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
            <h2 class="text-base font-semibold text-gray-900">Success messages</h2>

            <div>
                <label for="success_message" class="block text-sm font-medium text-gray-700 mb-1">
                    After subscribe <span class="text-gray-400 font-normal">(double opt-in enabled)</span>
                </label>
                <textarea id="success_message" name="success_message" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ember-400 resize-none"
                          maxlength="300" required>{{ old('success_message', $config['success_message']) }}</textarea>
            </div>

            <div>
                <label for="success_message_no_optin" class="block text-sm font-medium text-gray-700 mb-1">
                    After subscribe <span class="text-gray-400 font-normal">(double opt-in disabled)</span>
                </label>
                <textarea id="success_message_no_optin" name="success_message_no_optin" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ember-400 resize-none"
                          maxlength="300" required>{{ old('success_message_no_optin', $config['success_message_no_optin']) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="bg-ember-500 hover:bg-ember-600 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                Save settings
            </button>
        </div>
    </form>
</div>
@endsection
