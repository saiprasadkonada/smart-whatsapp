@extends('admin.layouts.app')
@section('panel')
    <section>
        <div class="container-fluid p-0">
            <div class="card ">
                <div class="card-header">
                    <h4 class="card-title">{{ translate('Whatsapp List')}}</h4>
                </div>

                <div class="card-filter">
                    <form action="{{route('admin.whatsapp.search')}}" method="GET">
                        <div class="filter-form">
                            <div class="filter-item">
                                <select name="status" class="form-select">
                                    <option value="all" @if(@$status == "all") selected @endif>{{translate('All')}}</option>
                                    <option value="pending" @if(@$status == "pending") selected @endif>{{translate('Pending')}}</option>
                                    <option value="schedule" @if(@$status == "schedule") selected @endif>{{translate('Schedule')}}</option>
                                    <option value="fail" @if(@$status == "fail") selected @endif>{{translate('Fail')}}</option>
                                    <option value="delivered" @if(@$status == "delivered") selected @endif>{{translate('Delivered')}}</option>
                                    <option value="processing" @if(@$status == "processing") selected @endif>{{translate('Processing')}}</option>
                                </select>
                            </div>

                            <div class="filter-item">
                                <input type="text" autocomplete="off" name="search" placeholder="{{ translate('Search with Email or Recipient Number')}}" class="form-control" id="search" value="{{@$search}}">
                            </div>

                            <div class="filter-item">
                                <input type="text" class="form-control datepicker-here" name="date" value="{{@$searchDate}}" data-range="true" data-multiple-dates-separator=" - " data-language="en" data-position="bottom right" autocomplete="off" placeholder="{{ translate('From Date-To Date')}}" id="date">
                            </div>

                            <div class="filter-action">
                                <button class="i-btn info--btn btn--md" type="submit">
                                    <i class="fas fa-search"></i> {{ translate('Search')}}
                                </button>
                                <button class="i-btn danger--btn btn--md">
                                    <a class="text-white" href="{{ route('admin.whatsapp.index')}}">
                                        <i class="las la-sync"></i>  {{translate('reset')}}
                                    </a>
                                </button>
                                <div class="statusUpdateBtn d-none">
                                    <a class="i-btn success--btn btn--md statusupdate"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Status Update"
                                            data-bs-toggle="modal"
                                            data-bs-target="#smsstatusupdate"
                                            type="submit">
                                        <i class="fas fa-gear"></i> {{translate('Action')}}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body px-0">
                    <div class="responsive-table">
                        <table>
                            <thead>
		                        <tr>
                                    <th>
                                    	<div class="d-flex align-items-center">
                                    		<input class="form-check-input mt-0 me-2 checkAll" type="checkbox" value="" aria-label="Checkbox for following text input"> <span>#</span>
                                    	</div>
                                    </th>
		                            <th>{{ translate('User')}}</th>
		                            <th>{{ translate('Gateway')}}</th>
		                            <th>{{ translate('To')}}</th>
		                            <th>{{ translate('Time & Date')}}</th>
		                            <th>{{ translate('Status')}}</th>
		                            <th class="text-center">{{ translate('Action')}}</th>
		                        </tr>
		                    </thead>

                            <tbody>
                                @forelse($smslogs as $smsLog)

                                    <tr class="@if($loop->even)@endif">
                                        <td class="lh-1" data-label="{{ translate('Id')}}">
                                            <input class="form-check-input mt-0 me-2" type="checkbox" name="whatsappid" value="{{$smsLog->id}}" aria-label="Checkbox for following text input">
                                            {{$loop->iteration}}
                                        </td>

                                         <td data-label="{{ translate('User')}}">
                                             @if($smsLog->user_id)
                                                <a href="{{route('admin.user.details', $smsLog->user_id)}}" class="fw-bold text-dark">{{$smsLog->user?->name}}</a>
                                            @else
                                                <span>{{ translate('Admin')}}</span>
                                            @endif
                                        </td>

                                        <td data-label="{{ translate('Gateway')}}">
                                             @if($smsLog->whatsappGateway)
                                                <p class="mb-1">
                                                    {{ translate('WhatsApp ')}}
                                                </p>

                                                <span class="bg--lite--info text--info rounded px-2 py-1 d-inline-block fs--12">{{$smsLog->whatsappGateway->name}}</span>
                                            @else
                                                <span>{{translate('N/A')}}</span>
                                            @endif
                                        </td>
                                      
                                        <td data-label="{{ translate('To')}}">
                                            {{$smsLog->to}}
                                          
                                            <p>
                                                @php
                                                    $getMessageCountWord = $general->whatsapp_word_count;
                                                    $messages = str_split($smsLog->message,$getMessageCountWord);
                                                    $totalMessage = count($messages);
                                                @endphp
                                                <span class="badge badge--success">
                                                    <i class="las la-coins"></i>
                                                     {{$totalMessage}} {{ translate('Credit')}}
                                                </span>
                                            </p>
                                        </td>

                                        <td data-label="{{ translate('Date & Time')}}">
                                            <p class="mb-1">
                                                {{translate("Initiated:")}}
                                                <span class="text-muted">{{getDateTime($smsLog->created_at)}}
                                                </span>
                                            </p>

                                            @if(!is_null($smsLog->initiated_time))
                                                <p>
                                                    {{translate("Schedule: ")}} <span class="text-muted">{{getDateTime($smsLog->initiated_time)}}</span>
                                                </p>
                                            @else
                                                <p>{{translate("Schedule: ")}}{{translate('N/A')}}</p>
                                            @endif
                                            @if(!is_null($smsLog->delivered_at))
                                            <p class="mb-1">
                                                {{translate("Delivered At: ")}}<span class="text-muted">{{getDateTime($smsLog->delivered_at)}}</span>
                                            </p>
                                            @else
                                                <p>{{translate("Delivered At: ")}}{{translate('N/A')}}</p>
                                            @endif

                                        </td>


                                        <td data-label="{{ translate('Status')}}">

                                            <div class="d-flex align-items-center gap-2">
                                                @if($smsLog->status == 1)
                                                    <span class="badge badge--primary">{{ translate('Pending ')}}</span>
                                                @elseif($smsLog->status == 2)
                                                    <span class="badge badge--info">{{ translate('Schedule')}}</span>
                                                @elseif($smsLog->status == 3)
                                                    <span class="badge badge--danger">{{ translate('Fail')}}</span>
                                                @elseif($smsLog->status == 4)
                                                    <span class="badge badge--success">{{ translate('Delivered')}}</span>
                                                @elseif($smsLog->status == 5)
                                                    <span class="badge badge--primary">{{ translate('Processing')}}</span>
                                                @endif
                                                <a class="s_btn--coral text--light statusupdate"
                                                    data-id="{{$smsLog->id}}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Status Update"
                                                    data-bs-target="#smsstatusupdate"
                                                    ><i class="las la-info-circle"></i>
                                                </a>
                                            </div>
                                        </td>

                                        <td data-label={{ translate('Action')}}  class="text-center">
                                            <div class="d-flex align-items-center justify-content-md-center justify-content-end gap-3">
                                                <a class="i-btn primary--btn btn--sm details"
                                                data-message="{{$smsLog->message}}"
                                                data-response_gateway="{{$smsLog->response_gateway}}"
                                                data-bs-placement="top" title="Details"
                                                data-bs-toggle="modal"
                                                data-bs-target="#smsdetails"
                                                    ><i class="las la-desktop"></i></a>

                                                <a href="javascript:void(0)" class="i-btn danger--btn btn--sm smsdelete"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#delete"
                                                    data-delete_id="{{$smsLog->id}}"
                                                    ><i class="las la-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ translate('No Data Found')}}</td>
                                    </tr>
                                @endforelse
                            </tbody>
		                </table>
	            	</div>
	                <div class="m-3">
	                	{{$smslogs->appends(request()->all())->onEachSide(1)->links()}}
					</div>
	            </div>
	        </div>
	    </div>
    </section>


    <div class="modal fade" id="smsstatusupdate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.whatsapp.status.update')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <input type="hidden" name="smslogid">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-header bg--lite--violet">
                                <div class="card-title text-center text--light">{{ translate('WhatsApp Status Update')}}</div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="status" class="form-label">{{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-control" name="status" id="status" required>
                                        <option value="" selected="" disabled="">{{ translate('Select Status')}}</option>
                                        <option value="1">{{ translate('Pending')}}</option>
                                        <option value="4">{{ translate('Success')}}</option>
                                        <option value="3">{{ translate('Fail')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal_button2 modal-footer">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn success--btn btn--md">{{ translate('Submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="smsdetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg--lite--violet">
                            <div class="card-title text-center text--light">{{ translate('Message')}}</div>
                        </div>
                        <div class="card-body mb-3">
                            <p class="" id="message--text"></p>
                        </div>
                    </div>
                </div>

                <div class="modal_button2 modal-footer">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.whatsapp.delete')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <div class="modal_body2">
                        <div class="modal_icon2">
                            <i class="las la-trash"></i>
                        </div>
                        <div class="modal_text2 mt-3">
                            <h6>{{ translate('Are you sure to delete this message from log')}}</h6>
                        </div>
                    </div>
                    <div class="modal_button2 modal-footer">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn danger--btn btn--md">{{ translate('Delete')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('script-push')
<script>
	(function($){
		"use strict";
		$('.statusupdate').on('click', function(){
			var modal = $('#smsstatusupdate');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

		$('.details').on('click', function(){
			var modal = $('#smsdetails');
			var message = $(this).data('message');
			var response_gateway = $(this).data('response_gateway');
			$("#message--text").html(`${message} :: <span class="text-danger"> ${response_gateway} </span>`);
			modal.modal('show');
		});

		$('.smsdelete').on('click', function(){
			var modal = $('#delete');
			modal.find('input[name=id]').val($(this).data('delete_id'));
			modal.modal('show');
		});

        $('.checkAll').click(function(){
            $('input:checkbox').not(this).prop('checked', this.checked);
        });

        $('.statusupdate').on('click', function(){
            var modal = $('#smsstatusupdate');
            var newArray = [];
            $("input:checkbox[name=whatsappid]:checked").each(function(){
                newArray.push($(this).val());
            });
            modal.find('input[name=smslogid]').val(newArray.join(','));
            modal.modal('show');
        });
	})(jQuery);
</script>
@endpush
