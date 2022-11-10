@if($errors->all())
    @if(session('error'))
        <div class="alert dark alert-icon alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <i class="icon fa-warning" aria-hidden="true"></i> {{ session('error') }}
        </div>
    @endif
    @foreach($errors->all() as $message)
        <div class="alert dark alert-icon alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">x</span>
            </button>
            <i class="icon fa-warning" aria-hidden="true"></i>{{ $message }}
        </div>
    @endforeach
@endif
@if(session('success'))
    <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
        <i class="icon wb-info" aria-hidden="true"></i>{{ session('success') }}
    </div>
@endif