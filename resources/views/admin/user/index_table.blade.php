@if(count($users))
    <div class="col-lg-12">
        <div class="table-wrap">
            <table class="edit-car-info">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Роль</th>
                    <th>Телефон</th>
                    <th>Логин</th>
                    <th>Регион</th>
                    <th>Баланс</th>
                    <th>Редактирование</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td data-label="ID">{{ $user->id }}</td>
                        <td data-label="Имя">{{ $user->name }}</td>
                        <td data-label="Роль">{{ $user->role->name }}</td>
                        <td data-label="Телефон">{{ $user->phone }}</td>
                        <td data-label="Логин">{{ $user->login }}</td>
                        <td data-label="Регион">{{ $user->region }}</td>
                        <td data-label="Баланс">{{ $user->balance }} руб.</td>
                        <td class="edit" data-label="Редактирование">
                            <a href="{{ route('admin.user.show', ['id' => $user->id]) }}"><i class="far fa-edit"></i></a>&nbsp
                            <form action="{{ route('admin.user.delete') }}" method="post">
                                @csrf
                                <input name="user" type="hidden" value="{{$user['id']}}">
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