@foreach($users as $user)
    <div class="col-lg-4 col-md-6">
        <div class="personal-box">
            <div class="avatar">
                <img src="{{ asset('img/profile-temp.png') }}" alt="">
            </div>
            <div class="pos">{{ $user->role->name }}</div>
            <a href="{{ route('profile.pull', ['id' => $user->id]) }}"><h3>{{ $user->name }}</h3></a>
            @switch($user->role_id)
                @case(1)
                    <div class="add">{{ $user->address }}</div>
                    <div class="balance">Баланс: <strong>{{ $user->balance }} <span>рублей</span></strong></div>
                    @break
                @case(3)
                    <div class="balance">Номер машины: <strong>{{ $user->region }}</strong></div>
                    @break
            @endswitch
        </div>
    </div>
@endforeach
<div class="col-md-12">
    {{ $users->links() }}
</div>
