<form id="order-form" method="POST" action="{{ route('admin.user.create') }}">
    @csrf
    <div class="row">
        <div class="col-lg-9">
            <div class="row">
                <div class="col-lg-3 col-md-3">
                    <div class="form-item">
                        <legend>Логин</legend>
                        <input type="text" name="login" required autofocus>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3">
                    <div class="form-item">
                        <legend>Пароль</legend>
                        <input type="password" name="password" required>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3">
                    <div class="form-item">
                        <legend>Повторите пароль</legend>
                        <input type="password" name="password_confirmation" required>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3">
                    <div class="form-item">
                        <legend>Роль</legend>
                        <select name="role" required id="role_id">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-3">
                    <div class="form-item">
                        <legend>Имя</legend>
                        <input type="text" name="name" required>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3">
                    <div class="form-item">
                        <legend>Регион</legend>
                        <input type="text" name="region" required>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3">
                    <div class="form-item">
                        <legend>Телефон</legend>
                        <input type="text" class="tel-input" name="phone" required>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3">
                    <div class="form-item">
                        <legend>Адрес</legend>
                        <textarea name="address"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-item">
                        <legend>Категория</legend>
                        <select id="category_id" name="category_id">
                            <option value="">Пусто</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-3">
                    <button type="submit" class="btn btn-green btn-detail-page">Создать</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $('#order-form').on('submit', function(){
        let role_id = $('#role_id').val();
        if(role_id == 2 || role_id == 3){
            let category_id = $('#category_id').val();
            if(category_id == ''){
                alert('Выберите категорию!');
                return false;
            } else {
                return true
            }
        }
    })
</script>