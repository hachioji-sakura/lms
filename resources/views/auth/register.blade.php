@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="name_first" class="col-md-4 col-form-label text-md-right">氏</label>
                            <div class="col-md-3">
                                <input id="name_first" type="text" class="form-control{{ $errors->has('name_first') ? ' is-invalid' : '' }}" name="name_first" value="{{ old('name_first') }}" required autofocus>

                                @if ($errors->has('name_first'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name_first') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <label for="name_last" class="col-md-3 col-form-label text-md-right">名</label>
                            <div class="col-md-3">
                                <input id="name_last" type="text" class="form-control{{ $errors->has('name_last') ? ' is-invalid' : '' }}" name="name_last" value="{{ old('name_last') }}" required autofocus>

                                @if ($errors->has('name_last'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name_last') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kana_first" class="col-md-4 col-form-label text-md-right">氏</label>
                            <div class="col-md-3">
                                <input id="kana_first" type="text" class="form-control{{ $errors->has('kana_first') ? ' is-invalid' : '' }}" name="kana_first" value="{{ old('kana_first') }}" required autofocus>

                                @if ($errors->has('kana_first'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('kana_first') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <label for="kana_last" class="col-md-3 col-form-label text-md-right">名</label>
                            <div class="col-md-3">
                                <input id="kana_last" type="text" class="form-control{{ $errors->has('kana_last') ? ' is-invalid' : '' }}" name="kana_last" value="{{ old('kana_last') }}" required autofocus>

                                @if ($errors->has('kana_last'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('kana_last') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                          <div class="input-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="gender_2" {{ old('gender') ? 'checked' : '' }}　value=2 required autofocus>
                                <label class="form-check-label" for="gender_2">
                                    女性
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="gender_1" {{ old('gender') ? 'checked' : '' }}　value=1 required autofocus>
                                <label class="form-check-label" for="gender_1">
                                    男性
                                </label>
                            </div>
                          </div>
                        </div>
                        <div class="form-group row">
                            <label for="birth_day" class="col-md-4 col-form-label text-md-right">生年月日</label>

                            <div class="col-md-6">
                                <input id="birth_day" type="date" class="form-control{{ $errors->has('birth_day') ? ' is-invalid' : '' }}" name="birth_day" value="{{ old('birth_day') }}" required>

                                @if ($errors->has('birth_day'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('birth_day') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">メールアドレス</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">パスワード</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">パスワード（確認）</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
