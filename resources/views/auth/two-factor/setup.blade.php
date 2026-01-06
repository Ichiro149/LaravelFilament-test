@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">
                {{ __('profile.two_factor_setup') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('profile.two_factor_scan_qr') }}
            </p>
        </div>

        <div class="px-6 py-6">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="flex flex-col md:flex-row gap-8">
                {{-- QR Code --}}
                <div class="flex-shrink-0">
                    <div class="bg-white p-4 rounded-lg border border-gray-200 inline-block">
                        {!! $qrCode !!}
                    </div>
                </div>

                {{-- Instructions --}}
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('profile.two_factor_instructions') }}</h3>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600">
                        <li>{{ __('profile.two_factor_step1') }}</li>
                        <li>{{ __('profile.two_factor_step2') }}</li>
                        <li>{{ __('profile.two_factor_step3') }}</li>
                    </ol>

                    {{-- Manual Entry --}}
                    <div class="mt-6">
                        <p class="text-sm text-gray-600 mb-2">{{ __('profile.two_factor_manual_entry') }}</p>
                        <code class="block bg-gray-100 px-3 py-2 rounded text-sm font-mono break-all">{{ $secret }}</code>
                    </div>
                </div>
            </div>

            {{-- Verification Form --}}
            <form action="{{ route('two-factor.enable') }}" method="POST" class="mt-8 border-t border-gray-200 pt-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">
                            {{ __('profile.two_factor_verification_code') }}
                        </label>
                        <input type="text" 
                               name="code" 
                               id="code"
                               inputmode="numeric"
                               autocomplete="one-time-code"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-center text-lg tracking-widest"
                               placeholder="000000"
                               maxlength="6"
                               required>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            {{ __('profile.confirm_password') }}
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                               required>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <a href="{{ route('two-factor.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        {{ __('common.cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        {{ __('profile.two_factor_verify') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
