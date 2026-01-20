<!-- Account Panel -->
<div class="settings-panel" id="panel-account" data-panel="account">
    <div class="settings-card">
        <div class="settings-section">
            <div class="settings-section-header">
                <div class="settings-section-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="settings-section-info">
                    <h3 class="settings-section-title">{{ __('settings.account_title') }}</h3>
                    <p class="settings-section-description">{{ __('settings.account_description') }}</p>
                </div>
            </div>

            <form action="{{ route('settings.update-account') }}" method="POST" class="settings-form">
                @csrf
                @method('PUT')
                
                <div class="settings-form-group">
                    <label for="username" class="settings-form-label">{{ __('settings.username') }}</label>
                    <div class="settings-form-input-wrapper">
                        <span class="settings-form-input-prefix">@</span>
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            class="settings-form-input with-prefix @error('username') is-invalid @enderror" 
                            value="{{ old('username', auth()->user()->username) }}"
                            placeholder="{{ __('settings.username_placeholder') }}"
                            pattern="^[a-zA-Z0-9_]+$"
                            minlength="3"
                            maxlength="30"
                        >
                    </div>
                    <p class="settings-form-hint">{{ __('settings.username_hint') }}</p>
                    @error('username')
                        <p class="settings-form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="settings-form-group">
                    <label class="settings-form-label">{{ __('settings.email') }}</label>
                    <div class="settings-form-static">
                        {{ auth()->user()->email }}
                        @if(auth()->user()->email_verified_at)
                            <span class="settings-form-badge">{{ __('settings.verified') }}</span>
                        @endif
                    </div>
                    <p class="settings-form-hint">{{ __('settings.email_hint') }}</p>
                </div>

                <div class="settings-form-group">
                    <label class="settings-form-label">{{ __('settings.member_since') }}</label>
                    <div class="settings-form-static">
                        {{ auth()->user()->created_at->format('F j, Y') }}
                    </div>
                </div>

                <div class="settings-form-actions">
                    <button type="submit" class="settings-btn settings-btn-primary">
                        {{ __('settings.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
