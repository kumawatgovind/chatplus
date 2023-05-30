<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a>
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            <h1 class="mb-4 text-center" style="font-size: 26px;font-weight: 600;">{{ __('Forgot Password') }}</h1>
            <p>{{ __('Please enter your registered email address & we will send you a reset password link to set a new password.') }}</p>
        </div>
        
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        {{-- <x-auth-validation-errors class="mb-4" :errors="$errors" /> --}}

        <form method="POST" action="{{ route('admin.password.email') }}">
            @csrf

            <!-- Email Address -->
            <div class="form-group required {{ $errors->has('email') ? 'has-error' : '' }}">
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" type="email" name="email" :value="old('email')" autofocus />
                 @if($errors->has('email'))
                <span class="help-block">{{ $errors->first('email') }}</span>
                @endif
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('admin.login'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 pullright" href="{{ route('admin.login') }}">
                        {{ __('Back to login') }}
                    </a>
                @endif
                &nbsp;&nbsp;
                <x-button class="btn btn-block btn-primary btn-sm">
                    {{ __('Submit') }}
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
    .pull-left{
        float: left;
    }
    .pullright{
        margin-right: 46%;
    }
</style>
