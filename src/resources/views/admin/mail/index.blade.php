@extends('admin.layouts.app')
@section('panel')
<section>
    @php
        $jsonArray = json_encode($credentials);
    @endphp
    <div class="container-fluid p-0">
		<div class="table_heading d-flex align--center justify--between">
			<nav  aria-label="breadcrumb">
				  <ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{route('admin.mail.list')}}"> {{ translate('Mail Configuration')}}</a></li> 
				  </ol>
			</nav>
		</div>
		<div class="card ">
			<div class="card-header">
				<h4 class="card-title">{{ translate('Email Gateway List')}}</h4>
                <div class="d-flex justify-content-end">
                    <form action="javascript:void(0)" class="testDefaultGateway" method="post">
                        @csrf
                        <div class="input-group w-100">
                            <input type="email" name="email" class="form-control" placeholder="{{ translate('Type Mail Address') }}">
                            <button class="i-btn info--btn btn--md" id="basic-addon2" type="submit">{{ translate('Test Default Gateway')}}</button>
                        </div>
                    </form>
                    <a href="javascript:void(0);" class="ms-3 i-btn primary--btn btn--md text-white" data-bs-toggle="modal" data-bs-target="#addgateway" title=" {{ translate('Add New Gateway')}}">
                        <i class="fa-solid fa-plus"></i> {{translate('Add New')}}
                    </a>
                </div>
               
			</div>

			

			<div class="card-body px-0">
				<div class="responsive-table">
					<table>
						<thead>
							<tr>
                                <th>{{ translate('Sl No') }}</th>
                                <th>{{ translate('Gateway type') }}</th>
								<th>{{ translate('Name')}}</th>
								<th>{{ translate('Address')}}</th>
                                <th>{{ translate('Status')}}</th>
								<th>{{ translate('Default')}}</th>
								<th>{{ translate('Action')}}</th>
							</tr>
						</thead>
						@forelse($gateways as $log)
                            
							<tr class="@if($loop->even)@endif">
								<td class="d-none d-sm-flex align-items-center">
									
									{{$loop->iteration }}
                                   
								</td>

                                <td data-label="{{ translate('Name')}}" >
                                   {{strToUpper($log?->type)}} 
                                 
								</td>

                                <td data-label="{{ translate('Name')}}">
                                    
                                    {{$log?->name}}
								
								</td>


								<td data-label="{{ translate('Address')}}">
                                    {{$log?->address}}
								</td>
                                <td data-label="{{ translate('Status')}}">
                                    @if($log?->status == 1)
                                        <span class="badge badge--success">{{ translate('Active')}}</span>
                                    @else
                                        <span class="badge badge--danger">{{ translate('Inactive')}}</span>
                                    @endif
                                </td>

							
                                <td class="text-center" data-label="{{ translate('default')}}">
                                    <div class="d-flex justify-content-md-start justify-content-end">
                                        @if($log?->is_default == 1)
                                            <i class="las la-check-double text--success" style="font-size:32px"></i>
                                        @else
                                            <label class="switch">
                                                <input type="checkbox" class="default_status" data-id="{{$log->id}}" value="1" name="default_value" id="default_gateway">
                                                <span class="slider"></span>
                                            </label>
                                        @endif
                                    </div>
                                </td>

								<td data-label={{ translate('Action')}}>
									<div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
										
                                        <a class="i-btn info--btn btn--sm gateway-details {{ $log->primary_status == 1 ? "d-none" : '' }}"
                                            data-driver_information="{{json_encode($log->mail_gateways)}}"
                                            data-bs-placement="top" title="Gateway Information"
                                            data-bs-toggle="modal"
                                            data-bs-target="#gatewayInfo">
                                            <i class="las la-info-circle"></i>
                                        </a>    
										<a href="javascript:void(0)" class="i-btn success--btn btn--sm edit-gateway" 
                                            data-bs-toggle="modal"
                                            data-bs-target="#editgateway"
                                            data-id="{{$log?->id}}"
                                            data-gateway_type="{{$log?->type}}"
                                            data-gateway_name="{{$log?->name}}"
                                            data-gateway_address="{{$log?->address}}"
                                            data-gateway_driver_information="{{json_encode($log?->mail_gateways)}}"
                                            data-gateway_status="{{$log?->status}}">
                                            <i class="las la-pen"></i>
                                        </a>
	
										<a href="javascript:void(0)" class="i-btn danger--btn btn--sm delete"
											data-bs-toggle="modal"
											data-bs-target="#gatewayDelete"
											data-delete_id="{{$log->id}}"
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
					</table>
				</div>
				<div class="m-3">
					{{$gateways->appends(request()->all())->onEachSide(1)->links()}}
				</div>
			</div>
		</div>
       
	</div>
</section>

<div class="modal fade" id="gatewayInfo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog nafiz">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card">
                    <div class="card-header bg--lite--violet">
                        <div class="card-title text-center text--light">{{ translate('Gateway Information')}}</div>
                    </div>
                    <div class="card-body">
                        <div class="driver-info"></div>
                    </div>
                </div>
            </div>

            <div class="modal_button2 modal-footer">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Close')}}</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="gatewayDelete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.mail.delete')}}" method="GET">
        		@csrf
        		<input type="hidden" name="id" value="">
	            <div class="modal_body2">
	                <div class="modal_icon2">
	                    <i class="las la-trash"></i>
	                </div>
	                <div class="modal_text2 mt-4">
	                    <h5>{{ translate('Are you sure to delete this gateway')}}</h5>
	                </div>
	            </div>

				<div class="modal_button2 modal-footer">
					<div class="d-flex align-items-center justify-content-center gap-3">
						<button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
						<button type="submit" class="i-btn primary--btn btn--md">{{ translate('Delete')}}</button>
					</div>
				</div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editgateway" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Edit New Mail Gateway')}}</h5>
                <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
            </div>

            <form action="{{ route('admin.mail.update')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-item mb-3">
                                
                                <label for="gateway_type_edit" class="form-label"> {{ translate('Gateway Type')}} <sup class="text--danger">*</sup></label>
                                
                                <select class="form-select text-uppercase select-gateway-type gateway_type" name="type" required="" id="gateway_type_edit"></select> 
                            </div>
                            <div class="row mb-3 newdataadd"></div>
                            <div class="row mb-3 oldData"></div>
                            <div class="row">
                                <div class="form-item mb-3 col-lg-6">
                                    <label for="name" class="form-label"> {{ translate('From Name')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder=" {{ translate('Enter From Name')}}" required>
                                </div>
                                <div class="form-item mb-3 col-lg-6">
                                    <label for="address" class="form-label"> {{ translate('From address')}} <sup class="text--danger">*</sup></label>
                                    <input type="email" class="form-control" id="address" name="address" placeholder=" {{ translate('Enter From Address')}}" required>
                                </div>
                            </div>
                    
                            <div class="form-item mb-3">
                                <label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                <select class="form-select" name="status" id="status" required>
                                    <option class="active" value="1"> {{ translate('Active')}}</option>
                                    <option class="inactive" value="0"> {{ translate('Inactive')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                        <button type="submit" class="i-btn primary--btn btn--md"> {{ translate('Submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addgateway" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Add New Mail Gateway')}}</h5>
                <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
            </div>

            <form action="{{route('admin.mail.create')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-item mb-3">
                                <label for="add_gateway_type" class="form-label"> {{ translate('Gateway Type')}} <sup class="text--danger">*</sup></label>
                                <select class="form-select gateway_type" name="type" required="" id="add_gateway_type">
                                    <option value=""selected disabled>{{ translate("Select a gateway type") }}</option>
                                    @foreach($credentials as $key=>$credential)
                                        <option value="{{strToLower($key)}}">{{strtoupper($key)}}</option>
                                    @endforeach
                                </select>
                            </div>
                          
                            <div class="row mb-3 newdataadd"></div>
                            <div class="row">
                                <div class="form-item mb-3 col-lg-6">
                                    <label for="name" class="form-label"> {{ translate('From Name')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder=" {{ translate('Enter From Name')}}" required>
                                </div>
                                <div class="form-item mb-3 col-lg-6">
                                    <label for="address" class="form-label"> {{ translate('From address')}} <sup class="text--danger">*</sup></label>
                                    <input type="email" class="form-control" id="address" name="address" placeholder=" {{ translate('Enter From Address')}}" required>
                                </div>
                            </div>
                      
                        
                            <div class="form-item mb-3">
                                <label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                <select class="form-select" name="status" id="status" required>
                                    <option value="1"> {{ translate('Active')}}</option>
                                    <option value="0"> {{ translate('Inactive')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                        <button type="submit" class="i-btn primary--btn btn--md"> {{ translate('Submit')}}</button>
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
            var oldType = '';
            var oldInfo = [];
            $('.gateway-details').on('click', function(){
                $('.driver-info').empty();
                var modal = $('#gatewayInfo');
                var driver = $(this).data('driver_information');
                $.each(driver, function(key, value) {
                  var paragraph = $('<p class="d-flex justify-content-start align-items-center "><span class="fw-bold text-capitalize col-3">' + key + ' </span> <span class="col-9">: ' + value + ' </span></p>');
                  $('.driver-info').append(paragraph);
              });
                modal.modal('show');
            });

            $('.delete').on('click', function(){

                var modal = $('#gatewayDelete');
                modal.find('input[name=id]').val($(this).data('delete_id'));
                modal.modal('show');
            });




            $('.edit-gateway').on('click', function() {
                $('.newdataadd').empty();
                $('.oldData').empty();
                $('.select-gateway-type').empty();
                $('.active').attr("selected",false);
                $('.inactive').attr("selected",false);
                $('.gatewayType').attr("selected",false);
               
                var modal = $('#editgateway');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('input[name=name]').val($(this).data('gateway_name'));
                modal.find('input[name=address]').val($(this).data('gateway_address'));
                
                
                $(this).data('gateway_status') == 1 ? $('.active').attr("selected",true) : $('.inactive').attr("selected",true);
                oldType = $(this).data('gateway_type');
                var data = Object.keys(<?php echo $jsonArray ?>);
                var creds = <?php echo $jsonArray ?>;
                
                $.each(data, function(key, value) {
                    var option = $('<option class="text-uppercase gatewayType" value="'+ value +'">'+ value +'</option>');
                    $('.select-gateway-type').append(option);
                    if(oldType == value){
                        $('.gatewayType').attr("selected",true)
                    }
                });

                oldInfo = $(this).data('gateway_driver_information');
               
                $.each(oldInfo, function(key, value) {
                    if(key != 'encryption') { 

                        var filterkey = key.replace("_", " ");
                        var div   = $('<div class="form-item mb-3 col-lg-6"></div>');
                        var label = $('<label for="' + key + '" class="form-label text-capitalize">' + filterkey + '<sup class="text--danger">*</sup></label>');
                        var input = $('<input type="text" class="form-control" id="' + key + '" value="' + value + '" name="driver_information[' + key + ']" placeholder="Enter ' + filterkey + '" required>');
                    
                        div.append(label, input);
                        $('.oldData').append(div);
                        
                    }
                    else{
                        
                        
                        $.each(creds, function(k, v) {
                            
                            $.each(v, function(cred_key, cred_value) {
                            
                                if(cred_key == key) {
                                    
                                    var filterkey = key.replace("_", " ");
                                    var div   = $('<div class="form-item mb-3 col-lg-6"></div>');
                                    var label  = $('<label for="' + key + '" class="form-label text-capitalize">' + filterkey + '<sup class="text--danger">*</sup></label>');
                                    var select = $('<select class="form-select" name="driver_information[' + key + ']" id="'+key+'"></select>')
                                    $.each(cred_value, function(name, method) {
                                        
                                        var option = $('<option class="encryptionType" value="'+method+'">'+name+'</option>');
                                        
                                        select.append(option);
                                        if(method == value){
                                            option.attr("selected",true)
                                        }
                                        
                                    }); 
                                    
                                    
                                    div.append(label,select);
                                    $('.oldData').append(div);

                                }
                            });
                        });
                      
                    }
                   
               
                  

                });

                modal.modal('show');
            });

            $('.default_status').on('change', function(){
                
                const default_value = $(this).val();
                const id = $(this).attr('data-id');
                $.ajax({
                    method:'get',
                    url: "{{ route('admin.mail.default.status') }}",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data:{
                    'default_value' : default_value,
                    'id' :id
                    },
                    dataType: 'json'
                }).then(response => {
                    if(response.status){
                        notify('success', 'Recommended Status Updated Successfully');
                        window.location.reload()
                    }
                    else{
                        notify('error', 'Inactive gateway can not be the default gateway');
                        window.location.reload()
                    }
                })


		    });

            $('.testDefaultGateway').on('submit', function(){

                var button = $(this).find('button[type="submit"]');
                var email = $(this).find('input[name=email]').val();
                if (button.hasClass('disabled')) {
                    return;
                }

                button.addClass('disabled').append('<span class="loading-spinner spinner-border spinner-border-sm" aria-hidden="true"></span> ');

                $.ajax({
                    method:'post',
                    url: "{{ route('admin.mail.test') }}",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data:{
                    'email' : email,
                    },
                    dataType: 'json'
                }).then(response => {
                    button.find('.loading-spinner').remove();
                    button.removeClass('disabled');
                    if(response.status){
                        notify('success', "Successfully sent mail to "+response.address+", please check your inbox or spam");
                       
                    }
                    else{
                        notify('error', "Mail Configuration Error, Please check your "+response.address+" configuration properly");
                       
                    }
                })


		    });

            $('.gateway_type').on('change', function(){
                
                $('.newdataadd').empty();
                var data = <?php echo $jsonArray ?>[this.value];
                var newType = this.value;
                
                if(newType != oldType){
                    
                    $.each(data, function(key, v) {
                        $('.oldData').empty();
                        var filterkey = key.replace("_", " ");
                        var div   = $('<div class="form-item mb-3 col-lg-6"></div>');
                        if(key != 'encryption'){
                            var label = $('<label for="' + key + '" class="form-label text-capitalize">' + filterkey + '<sup class="text--danger">*</sup></label>');
                            var input = $('<input type="text" class="form-control" id="' + key + '" name="driver_information[' + key + ']" placeholder="Enter ' + filterkey + '" required>');
                            div.append(label, input);
                            $('.newdataadd').append(div);
                        }
                        else{
                           
                            var label  = $('<label for="' + key + '" class="form-label text-capitalize">' + filterkey + '<sup class="text--danger">*</sup></label>');
                            var select = $('<select class="form-select" name="driver_information[' + key + ']" id="'+key+'"></select>')
                            $.each(v, function(name, method) {
                                var option = $('<option value="'+method+'">'+name+'</option>')
                                select.append(option);
                            }); 
                            div.append(label,select);
                            $('.newdataadd').append(div);
                        }
                    });
                }
                else{
              
                    $.each(oldInfo, function(key, value) {
                    var filterkey = key.replace("_", " ");
                    var div   = $('<div class="form-item mb-3 col-lg-6"></div>');
                    var label = $('<label for="' + key + '" class="form-label text-capitalize">' + filterkey + '<sup class="text--danger">*</sup></label>');
                    var input = $('<input type="text" class="form-control" id="' + key + '" value="' + value + '" name="driver_information[' + key + ']" placeholder="Enter ' + filterkey + '" required>');
                    div.append(label, input);
                    $('.oldData').append(div);
                });
                }
            });
        })(jQuery);
    </script>
@endpush
