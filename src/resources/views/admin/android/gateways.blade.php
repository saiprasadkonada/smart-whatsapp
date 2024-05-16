@extends('admin.layouts.app')
@section('panel')
<section>
    <div class="container-fluid p-0">
        <div class="row gy-4">
            @include('admin.sms_gateway.sms_gateway_tab')
            <div class="col">
                <div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"> {{ translate('Android Gateway List')}}</h4>
                            
                            <div class="d-flex gap-3">
                                <a href="javascript:void(0);" class="i-btn primary--btn btn--md text-white" data-bs-toggle="modal" data-bs-target="#createandroid" title="{{ translate('Create New Android GW') }}">
                                <i class="fa-solid fa-plus"></i> {{translate('Add New')}}
                                </a>
                                <a href="javascript:void(0);" class="i-btn info--btn btn--md text-white" data-bs-toggle="modal" data-bs-target="#addApkLink" title="{{translate('Add APK file Link')}}">
                                    {{translate('Add APK File Link')}}
                                </a>
                            </div>
                        </div>
                        <div class="card-body px-0">
                            <div class="responsive-table">
                                <table >
                                    <thead>
                                    <tr>
                                        <th>{{ translate('Name') }}</th>
                                        <th>{{ translate('Password') }}</th>
                                        <th>{{ translate('Status') }}</th>
                                        <th>{{ translate('SIM List') }}</th>
                                        <th>{{ translate('Action') }}</th>
                                    </tr>
                                    </thead>
                                    @forelse($androids as $android)
                                        <tr class="@if($loop->even)@endif">
                                            <td data-label="{{ translate('Name') }}">
                                                <span class="text-dark">{{$android->name}}</span>
                                            </td>

                                            <td data-label="{{ translate('Password') }}">
                                                <span class="text-dark">{{$android->show_password}}</span>
                                            </td>

                                            <td data-label="{{ translate('Status') }}">
                                                @if($android->status == 1)
                                                    <span class="badge badge--success">{{ translate('Active') }}</span>
                                                @else
                                                    <span class="badge badge--danger">{{ translate('Inactive') }}</span>
                                                @endif
                                            </td>

                                            <td data-label="{{ translate('list')}}">
                                                <a href="{{route('admin.sms.gateway.android.sim.index', $android->id)}}" class="badge badge--primary p-2">{{ translate('View All ')."(".count($android->simInfo).")"}}</a>
                                            </td>

                                            <td data-label="{{translate('Action')}}">
                                                <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                                    <a class="i-btn info--btn btn--sm android" data-bs-toggle="modal" data-bs-target="#updateandroid" href="javascript:void(0)"
                                                    data-id="{{$android->id}}"
                                                    data-name="{{$android->name}}"
                                                    data-password="{{$android->show_password}}"
                                                    data-status="{{$android->status}}"><i class="las la-pen"></i></a>
                                                <a class="i-btn danger--btn btn--sm delete" data-bs-toggle="modal" data-bs-target="#deleteandroidApi" href="javascript:void(0)" data-id="{{$android->id}}"><i class="las la-trash"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="100%">{{ translate('No Data Found') }}</td>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>
                            <div class="m-3">
                                {{$androids->appends(request()->all())->onEachSide(1)->links()}}
                            </div>
                        </div>
                    </div>

                    
                </div>


                <div class="modal fade" id="addApkLink" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{route('admin.sms.gateway.android.link.store')}}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="card">
                                        <div class="card-header bg--lite--violet">
                                            <div class="card-title text-center text--light">{{ translate('Add a download link for users') }}</div>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="link" class="form-label">{{ translate('App Link') }} <sup class="text--danger">*</sup></label>
                                                <input type="text" class="form-control" id="app_link" name="app_link" placeholder="{{ translate('Insert link')}}" value="{{ $general->app_link }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal_button2 modal-footer">
                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                        <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                                        <button type="submit" class="i-btn danger--btn btn--md">{{ translate('Submit')}}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="createandroid" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{route('admin.sms.gateway.android.store')}}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="card">
                                        <div class="card-header bg--lite--violet">
                                            <div class="card-title text-center text--light">{{ translate('Add New Android Gateway') }}</div>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">{{ translate('Name') }} <sup class="text--danger">*</sup></label>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="{{ translate('Enter Name')}}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="password" class="form-label">{{ translate('Password') }} <sup class="text--danger">*</sup></label>
                                                <input type="password" class="form-control" id="password" name="password" placeholder="{{ translate('Enter Password')}}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="password_confirmation" class="form-label">{{ translate('Confirm Password') }} <sup class="text--danger">*</sup></label>
                                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{ translate('Confirm Password') }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="status" class="form-label">{{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                                <select class="form-control" name="status" id="status" required>
                                                    <option value="1">{{ translate('Active') }}</option>
                                                    <option value="2">{{ translate('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal_button2 modal-footer">
                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                        <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                                        <button type="submit" class="i-btn danger--btn btn--md">{{ translate('Submit')}}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="updateandroid" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{route('admin.sms.gateway.android.update')}}" method="POST">
                                @csrf
                                <input type="hidden" name="id">
                                <div class="modal-body">
                                    <div class="card">
                                        <div class="card-header bg--lite--violet">
                                            <div class="card-title text-center text--light">{{ translate('Update Android Gateway') }}</div>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="name-update" class="form-label">{{ translate('Name') }}<sup class="text--danger">*</sup></label>
                                                <input type="text" class="form-control" id="name-update" name="name" placeholder="{{ translate('Enter Name') }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="password-update" class="form-label">{{ translate('Password') }} <sup class="text--danger">*</sup></label>
                                                <input type="password" class="form-control" id="password-update" name="password" placeholder="{{ translate('Enter Password')}}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="status-update" class="form-label">{{ translate('Status') }} <sup class="text--danger">*</sup></label>
                                                <select class="form-control" id="status-update" name="status" required>
                                                    <option value="1">{{ translate('Active') }}</option>
                                                    <option value="2">{{ translate('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal_button2 modal-footer">
                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                        <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                                        <button type="submit" class="i-btn danger--btn btn--md">{{ translate('Submit') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="deleteandroidApi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{route('admin.sms.gateway.android.delete')}}" method="POST">
                                @csrf
                                <input type="hidden" name="id">
                                <div class="modal_body2">
                                    <div class="modal_icon2">
                                        <i class="las la-trash"></i>
                                    </div>
                                    <div class="modal_text2 mt-3">
                                        <h6>{{ translate('Are you sure to want delete this android gateway?') }}</h6>
                                    </div>
                                </div>
                                <div class="modal_button2 modal-footer">
                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                        <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                                        <button type="submit" class="i-btn danger--btn btn--md">{{ translate('Delete') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection


@push('script-push')
    <script>
        (function($){
            "use strict";
            $('.android').on('click', function(){
                var modal = $('#updateandroid');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('input[name=name]').val($(this).data('name'));
                modal.find('input[name=password]').val($(this).data('password'));
                modal.modal('show');
            });

            $('.delete').on('click', function(){
                var modal = $('#deleteandroidApi');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

