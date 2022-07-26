<div class="container-fluid user-page">
    <div class="row">
        <div class="col-lg-2">
            <div class="user-avatar">
                <img src="{{ asset('img/profile-temp.png') }}" alt="">
            </div>
        </div>
        <div class="col-lg-3">
            <div class="user-info-box">
                <h4>Должность</h4>
                {{ $user->role->name }}
            </div>
            <div class="user-info-box name">
                <h4>Имя</h4>
                {{ $user->name }}
            </div>
            <div class="user-info-box phone">
                <h4>Телефон</h4>
                {{ $user->phone }}
            </div>
        </div>
        <div class="col-lg-7">
            @switch($user->role->id)
                @case(4)
                    <div class="user-info-box float-right">
                    <a class="btn btn-green" href="{{ route('admin.app_version.index') }}">Версия приложения</a>
                    </div>
                @break
            @endswitch
        </div>
    </div>
</div>