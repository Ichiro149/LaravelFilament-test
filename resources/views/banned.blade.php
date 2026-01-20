@extends('layouts.app')

@section('title', __('Access Restricted'))

@push('styles')
<style>
    .banned-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
    }
    
    .banned-container {
        max-width: 600px;
        width: 100%;
        text-align: center;
    }
    
    .banned-icon {
        width: 120px;
        height: 120px;
        margin: 0 auto 30px;
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.05));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid rgba(239, 68, 68, 0.3);
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
    }
    
    .banned-icon svg {
        width: 60px;
        height: 60px;
        color: #ef4444;
    }
    
    .banned-title {
        font-size: 2rem;
        font-weight: 700;
        color: #ef4444;
        margin-bottom: 16px;
    }
    
    .banned-subtitle {
        font-size: 1.125rem;
        color: #a1a1aa;
        margin-bottom: 40px;
    }
    
    .banned-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 32px;
        text-align: left;
    }
    
    .banned-info-row {
        display: flex;
        justify-content: space-between;
        padding: 16px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .banned-info-row:last-child {
        border-bottom: none;
    }
    
    .banned-info-label {
        color: #71717a;
        font-size: 0.875rem;
    }
    
    .banned-info-value {
        color: #e5e5e5;
        font-weight: 500;
        text-align: right;
    }
    
    .banned-info-value.permanent {
        color: #ef4444;
    }
    
    .banned-info-value.temporary {
        color: #f59e0b;
    }
    
    .banned-message {
        margin-top: 24px;
        padding: 20px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        border-radius: 12px;
        color: #fca5a5;
        font-size: 0.938rem;
        line-height: 1.6;
    }
    
    .banned-footer {
        margin-top: 40px;
        color: #52525b;
        font-size: 0.875rem;
    }
    
    .banned-footer a {
        color: #f59e0b;
        text-decoration: none;
    }
    
    .banned-footer a:hover {
        text-decoration: underline;
    }
    
    .banned-logout {
        margin-top: 24px;
    }
    
    .banned-logout a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        color: #a1a1aa;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .banned-logout a:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #e5e5e5;
    }
</style>
@endpush

@section('content')
<div class="banned-page">
    <div class="banned-container">
        <div class="banned-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
        </div>
        
        <h1 class="banned-title">{{ __('Access Restricted') }}</h1>
        <p class="banned-subtitle">{{ __('Your access to this service has been restricted') }}</p>
        
        <div class="banned-card">
            <div class="banned-info-row">
                <span class="banned-info-label">{{ __('Ban Type') }}</span>
                <span class="banned-info-value">{{ $type }}</span>
            </div>
            
            <div class="banned-info-row">
                <span class="banned-info-label">{{ __('Reason') }}</span>
                <span class="banned-info-value">{{ $reason }}</span>
            </div>
            
            <div class="banned-info-row">
                <span class="banned-info-label">{{ __('Duration') }}</span>
                @if($ban->isPermanent())
                    <span class="banned-info-value permanent">{{ __('Permanent') }}</span>
                @else
                    <span class="banned-info-value temporary">
                        {{ __('Until') }} {{ $ban->expires_at->format('d.m.Y H:i') }}
                        <br>
                        <small style="color: #71717a;">({{ $ban->getRemainingTime() }})</small>
                    </span>
                @endif
            </div>
            
            <div class="banned-info-row">
                <span class="banned-info-label">{{ __('Date') }}</span>
                <span class="banned-info-value">{{ $ban->created_at->format('d.m.Y H:i') }}</span>
            </div>
            
            @if($ban->public_message)
                <div class="banned-message">
                    {{ $ban->public_message }}
                </div>
            @endif
        </div>
        
        <div class="banned-footer">
            <p>{{ __('If you believe this is a mistake, please contact') }} <a href="mailto:support@example.com">support@example.com</a></p>
        </div>
        
        @auth
        <div class="banned-logout">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                {{ __('Logout') }}
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
        @endauth
    </div>
</div>
@endsection
