<form id="order-form" method="POST" action="{{ route('admin.user.update') }}">
    @csrf
    <div class="row">
        <div class="col-lg-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li style="color:red">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Имя</legend>
                        <input type="text" name="name" value="{{ $user->name }}">
                    </div>
                </div>

                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Пароль (если хотите поменять)</legend>
                        <input type="password" name="password">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Повторите пароль</legend>
                        <input type="password" name="password_confirmation">
                    </div>
                </div>

                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Телефон</legend>
                        <input type="text" class="tel-input" name="phone" value="{{ $user->phone }}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Баланс</legend>
                        <input type="text" name="balance" value="{{ $user->balance }}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        @if($user->role_id == 3)
                            <legend>Номер машины</legend>
                        @else
                            <legend>Регион</legend>
                        @endif
                        <input type="text" name="region" value="{{ $user->region }}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="form-item">
                        <legend>Адрес</legend>
                        <textarea name="address">{{ $user->address }}</textarea>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Роль</legend>
                        <select id="role" name="role_id" class="nice-select">
                            <option value="{{ $user->role->id }}">{{ $user->role->name }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($user->role_id === 2 || $user->role_id === 3)
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Категория</legend>
                        @if( !empty($user->category_id) )
                            <select id="category" class="nice-select" name="category_id" required {{  count($user->cars) > 0 ? 'disabled' : ''  }}>
                                <option value="">Пусто</option>
                                @foreach($categories as $category)
                                    @if($user->category_id == $category->id)
                                        <option selected="selected" value="{{ $category->id }}">{{ $category->name }}</option>
                                    @else
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        @else
                            <select id="category" class="nice-select" name="category_id" required {{  count($user->cars) > 0 ? 'disabled' : ''  }}>
                                <option value="">Пусто</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Компания</legend>
                        <select id="role" name="company_id" class="nice-select">
                            <option value="">Пусто</option>
                                @foreach($companies as $company)
                                @if($user->company_id == $category->id)
                                    <option selected="selected" value="{{ $company->id }}">{{ $company->name }}</option>
                                @else
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="float-left">
                        <a href="{{ route('admin.users.index') }}"><button type="button" class="btn btn-danger">Назад</button></a>
                    </div>
                    <div class="float-right">
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        <button type="submit" class="btn btn-green">Изменить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>