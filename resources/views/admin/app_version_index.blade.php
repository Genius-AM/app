<div class="container user-page">
    <form id="order-form" method="POST" action="{{ route('admin.app_version.edit') }}">
        @csrf
        <div class="row">
            <div class="col-lg-3">
                <div class="user-info-box">
                    <h4 class="text-right">Текущая версия приложения</h4>
                    <p class="text-right">{{$version->version}}</p>
                </div>
            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-2">
                <div class="user-info-box">
                    <input type="hidden" name="id" value="{{ $version->id }}">
                    <button type="submit" class="btn btn-green btn-detail-page">Изменить</button>
                </div>
            </div>
        </div>
    </form>
</div>