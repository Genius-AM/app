<div class="row">
    <div class="col-lg-12">
        @if ($errors->any())
            <div class="alert alert_new alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
