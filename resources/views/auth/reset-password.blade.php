<x-guest-layout>
    <x-auth-card>

        @section('page-title')
            {{ __('Reset Password') }}
        @endsection

        @section('content')
            <div class="card-body">
                <div class="">
                    <h2 class="mb-3 f-w-600">{{ __('Reset Password') }}</h2>
                </div>
                <form method="POST" action="{{ route('password.update') }}" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <input type="email" class="form-control  @error('email') is-invalid @enderror" name="email"
                            id="emailaddress" value="{{ old('email') }}" required autocomplete="email" autofocus
                            placeholder="{{ __('Enter Your Email') }}">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                            required autocomplete="new-password" id="password"
                            placeholder="{{ __('Enter Your Password') }}">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="password" class="form-label">{{ __('Confirm Password') }}</label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                            name="password_confirmation" required autocomplete="new-password" id="password_confirmation"
                            placeholder="{{ __('Enter Your Password') }}">
                    </div>

                    <div class="d-grid">
                        <button type="submit" id="login_button"
                            class="btn btn-primary btn-block mt-2">{{ __('Reset Password') }}</button>
                    </div>
                </form>
            </div>
        @endsection
    </x-auth-card>
</x-guest-layout>