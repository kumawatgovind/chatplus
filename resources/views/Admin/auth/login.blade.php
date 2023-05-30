<?php 
$email = old('email');
$password = old('password');
$remember = false;
if((isset($_COOKIE['email']) || !empty($_COOKIE['email'])) && (isset($_COOKIE['password']) || !empty($_COOKIE['password']))){
    $email = $_COOKIE['email'];
    $password = $_COOKIE['password'];
    $remember = true;
}
?>
<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a>
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>
        <div class="mb-4 text-sm text-gray-600">
            <h1 class="mb-4 text-center" style="font-size: 26px;font-weight: 600;">{{ __('Login') }}</h1>
        </div>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        {{-- <x-auth-validation-errors class="mb-4" :errors="$errors ?? ''" /> --}}
        @include('flash.alert')
        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <!-- Email Address -->
            <div class="form-group required {{ $errors->has('email') ? 'has-error' : '' }}">
                <x-label for="email" :value="__('Email')" />
                <x-input id="email" class="block mt-1 w-full form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" type="email" name="email" value="{{ $email }}" autofocus maxlength="50" />
                @if($errors->has('email'))
                <span class="help-block">{{ $errors->first('email') }}</span>
                @endif
            </div>

            <!-- Password -->
            <div class="mt-4 form-group required {{ $errors->has('password') ? 'has-error' : '' }}">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                type="password"
                                name="password"
                                value="{{ $password }}"
                                autocomplete="current-password" maxlength="50"/>
                @if($errors->has('password'))
                <span class="help-block">{{ $errors->first('password') }}</span>
                @endif
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember" {{ ($remember) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>
            <div class="block items-center mt-4">
                @if (Route::has('admin.password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('admin.password.request') }}">
                        {{ __('Forgot Password?') }}
                    </a>
                @endif
            </div>
            <div class="items-center justify-cen mt-4">
                <x-button class="btn btn-block btn-primary btn-sm">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
<style>
    .required label::after {
        color: #cc0000;
        content: "*";
        font-weight: bold;
        margin-left: 5px;
    }
    .alert-success {
        color: #fff;
        background-color: #28a745;
        border-color: #23923d;
    }
    .alert {
        position: relative;
        padding: .75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: .25rem;
    }
    .alert .close, .alert .mailbox-attachment-close {
    color: #000;
    opacity: .2;
}
    .btn-block {
        display: block;
        width: 30%;
    }
    .btn-sm {
        padding: .25rem .5rem;
        font-size: .875rem;
        line-height: 1.5;
        border-radius: .2rem;
    }
    .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
        box-shadow: none;
    }
    .help-block {
        width: 100%;
        margin-top: .25rem;
        font-size: 80%;
        color: #dc3545;
    }
    .form-control.is-invalid, .was-validated .form-control:invalid {
        border-color: #dc3545;
        padding-right: 0rem;
        background-image: url("");
        background-repeat: no-repeat;
        background-position: right calc(.0rem) center;
        background-size: calc( .0rem);
    }
</style>