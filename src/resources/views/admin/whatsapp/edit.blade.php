@extends('admin.layouts.app')
@section('panel')

<section class="mt-3 rounded_box">
	<div class="container-fluid p-0 mb-3 pb-2">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="table_heading d-flex align--center justify--between">
                    <nav aria-label="breadcrumb">
					  	<ol class="breadcrumb">
					    	<li class="breadcrumb-item"><a href="{{route('admin.gateway.whatsapp.store')}}">{{ translate('Whatsapp Device')}}</a></li>
					    	<li class="breadcrumb-item" aria-current="page">{{$whatsapp->name}}</li>
					  	</ol>
					</nav>
                </div>
            </div>
        </div>
		<div class="row justify-content-center">
			<div class="col-xl-4">
                <form action="{{route('admin.gateway.whatsapp.update')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-header">
                            {{ translate('WhatsApp Device Edit')}}
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <input  type="hidden" name="id" value="{{$whatsapp->id}}">
                                <div class="col-md-12 mb-4">
                                    <label for="name">{{ translate('Name')}} <span class="text-danger" >*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror " name="name" id="name" value="{{$whatsapp->name}}" placeholder="{{ translate('Put Session Name (Any)')}}" required="" {{$whatsapp->status == 'connected' ? 'readonly' : ' '}}>
                                    @error('name')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label for="number">{{ translate('WhatsApp Number')}}  <span class="text-danger" >*</span>  </label>
                                    <input type="number" class="form-control @error('number') is-invalid @enderror " name="number" id="number" value="{{$whatsapp->number}}" placeholder="{{ translate('Put Your WhatsApp number here')}}" required>
                                    @error('number')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label for="min_delay">{{ translate('Message Minimum Delay Time')}} <span class="text-danger" >*</span></label>
                                    <input type="number" class="form-control @error('min_delay') is-invalid @enderror " name="min_delay" id="min_delay" value="{{$whatsapp->min_delay}}" required placeholder="{{ translate('Message minimum delay time in second')}}">
                                    @error('min_delay')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label for="max_delay">{{ translate('Message Maximum Delay Time')}} <span class="text-danger" >*</span></label>
                                    <input type="number" class="form-control @error('max_delay') is-invalid @enderror " name="max_delay" id="max_delay" value="{{$whatsapp->max_delay}}" required placeholder="{{ translate('Message maximum delay time in second')}}">
                                    @error('max_delay')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label for="multidevice">{{ translate('Status')}}
                                        <span class='text-danger' >*</span>
                                    </label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror " required>
                                        <option value="">{{ translate('Select One')}}</option>
                                        <option {{$whatsapp->status == 'initiate' ? 'selected' : ' '}} value="initiate">{{ translate('Initiate')}}</option>
                                        <option {{$whatsapp->status == 'connected' ? 'selected' : ' '}} value="connected">{{ translate('Connected')}}</option>
                                        <option {{$whatsapp->status == 'disconnected' ? 'selected' : ' '}} value="disconnected">{{ translate('Disconnected')}}</option>
                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 float-end">{{ translate('Update')}}</button>
                </form>
			</div>
		</div>
	</div>
</section>
@endsection
