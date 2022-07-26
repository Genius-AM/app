@if(count($users))
    <div class="col-lg-12">
        <div class="table-wrap">
            <table class="edit-car-info">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Устройство</th>
                    <th>Имя пользователя</th>
                    <th>Роль пользователя</th>
                    <th>Телефон</th>
                    <th>Логин</th>
                    <th>Редактирование</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td data-label="ID">{{ $user->id }}</td>
                        <td data-label="Имя">{{ $user->device_id }}</td>
                        <td data-label="Имя">{{ $user->name }}</td>
                        <td data-label="Роль">{{ $user->role->name }}</td>
                        <td data-label="Телефон">{{ $user->phone }}</td>
                        <td data-label="Логин">{{ $user->login }}</td>
                        <td class="edit" data-label="Редактирование">
                            <form action="{{ route('admin.user.device_delete', $user->id) }}" method="post">
                                @csrf
                                <input name="device" type="hidden" value="{{$user['id']}}">
                                <button type="submit" onclick="return confirm('Вы уверены?')"><i class="fas fa-times-circle"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif