@guest
    <div class="auth-box">
        <p><a href="{{ route('login') }}"><i class="demo-icon icon-user"></i> Вход</a></p>
    </div>
@endguest
@auth
    <div class="auth-box">
        <p>
            <a href="{{ route('profile.pull') }}" class="auth-button"><i class="demo-icon icon-user"></i> Профиль</a>
            <a href="{{ route('logout') }}">Выход</a>
        </p>
    </div>
@endauth