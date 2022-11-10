@extends('merchant.layouts.main')
@section('content')
  <div class="page">
    <div class="page-content">
      @include('merchant.shared.errors-and-messages')
      <div class="panel panel-bordered">
        <header class="panel-heading">
          <div class="panel-actions">
            <a href="{{route('merchant.driver.commissionfare.create')}}">
              <button type="button" title="@lang("$string_file.add_commission_fare")"
                      class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-plus"></i>
              </button>
            </a>
          </div>
          <h3 class="panel-title">
            <i class=" fa fa-taxi" aria-hidden="true"></i>
            @lang("$string_file.commission_fare") </h3>
        </header>
        <div class="panel-body">
          <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
            <thead>
            <tr>
              <th>@lang("$string_file.title")</th>
              <th>@lang("$string_file.start_range")</th>
              <th>@lang("$string_file.end_range")</th>
              <th>@lang("$string_file.discount")</th>
              <th>@lang("$string_file.action")</th>
            </tr>
            </thead>

            <tbody>
            @foreach($commissions as $commission)
              <tr>
                <td>
                  {{ $commission->name }}
                </td>
                <td>
                  {{ $commission->start_range }}
                </td>
                <td>
                  {{ $commission->end_range }}
                </td>
                <td>
                  {{ $commission->commission }}
                </td>
                <td>
                  <a class="btn btn-sm btn-warning" href="{{route('merchant.driver.commissionfare.create' , ['id' => $commission->id])}}">
                    <i class="wb-edit"></i>
                  </a>
                  <button class="btn btn-sm btn-danger menu-icon btn_delete action_btn"
                          onclick="if (confirm('Do you want to delete ?')) {$('#delete-fare-{{$commission->id}}').submit();}">
                    <i class="fa fa-trash"></i>
                  </button>
                  <form id="delete-fare-{{$commission->id}}" method="post" action="{{route('merchant.driver.commissionfare.destroy' , ['id' => $commission->id])}}">
                    @csrf
                  </form>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection


