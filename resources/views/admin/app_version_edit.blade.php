<div class="container user-page">
    <form id="order-form" method="POST" action="{{ route('admin.app_version.update') }}">
        @csrf
        <div class="row">
            <div class="col-lg-4 col-md-4">
                <div class="form-item">
                    <legend>Текущая версия</legend>
                    <input type="text" name="version" value="{{$version->version}}">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-4">
                <input type="hidden" name="id" value="{{ $version->id }}">
                <button type="submit" class="btn btn-green btn-detail-page">Сохранить</button>
            </div>
        </div>
    </form>
</div>