<div class="container container-auth">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-block">
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <br> <br>
                        <div align="center text-auth">
                            <h3 class="text-auth">Авторизация</h3>
                        </div>
                        <br><br>
                        <div class="col-md-4 offset-md-4">
                            <div class="form-item form-auth">
                                <input id="login" type="text" class="form-control{{ $errors->has('login') ? ' is-invalid' : '' }}" name="login" value="{{ old('login') }}" required autofocus >
                                <label for="Логин" class="passwordlable">Логин</label>
                                @if ($errors->has('login'))
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('login') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 offset-md-4">
                            <div class="form-item form-auth">
                                <input id="password" type="password" class="form-control" name="password" required>
                                <label for="Пароль" class="passwordlable">Пароль</label>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                                <a href="#" class="password-control" style="position: absolute;
top: -12px;
right: -57px;
margin-left: auto;
width: 20px;
height: 20px;
background: url(/images/Hide.png) 0 0 no-repeat;"></a>
                            </div>
                        </div>
                        <br>

                        <div class="col-md-4 offset-md-4">
                            <button type="submit" class="btn btn-send btn-detail-page btn-text">Войти</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = () => {
        $('body').on('click', '.password-control', function () {
            if ($('#password').attr('type') == 'password') {
                $(this).addClass('view');
                $('#password').attr('type', 'text');
            } else {
                $(this).removeClass('view');
                $('#password').attr('type', 'password');
            }
            return false;
        });
    }
</script>
