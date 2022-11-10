@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('users.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-user" aria-hidden="true"></i>
                        {{ $user->first_name." ".$user->last_name }}'s @lang("$string_file.favourite_drivers") </h3>
                </header>
                <div class="panel-body container-fluid">
                    <div class="row">
                        @forelse($user->FavouriteDriver as $driver)
                            <div class="col-xl-4 col-md-4 col-sm-6 col-12">
                                    <div class="text-center">
                                        <div class="mb-10">
                                            <img src="{{ get_image($driver->Driver->profile_image,'driver') }}"
                                                 class="rounded-circle"
                                                 alt="Card image" style="height:100px;width:100px;">
                                        </div>
                                        <div class="">
                                            <h4 class="user-name">{{ $driver->Driver->fullName }}</h4>
                                            <h4 class="user-info">{{ $driver->Driver->phoneNumber }}</h4>
                                            <h4 class="user-job">{{ $driver->Driver->email }}</h4>
                                        </div>
                                        <div class="mt-10 mb-20">
                                            <p>{{ $driver->Driver->CountryArea->CountryAreaName }}</p>
                                        </div>
                                    </div>
                            </div>
                        @empty
                            <div class="col-12 mb-10">
                                <p>@lang("$string_file.no_favourite_drivers")</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
