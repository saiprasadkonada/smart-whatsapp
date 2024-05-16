<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AndroidApi;
use App\Models\AndroidApiSimInfo;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Hash;

class AndroidApiController extends Controller
{
    public function index() {

    	$title 	  = "Android Gateway list";
    	$androids = AndroidApi::where('admin_id', auth()->guard('admin')->user()->id)->orderBy('id', 'DESC')->paginate(paginateNumber());
    	return view('admin.android.index', compact('title', 'androids'));
    }

    public function store(Request $request) {

    	$request->validate([
			'name'     => ['required', 'username_format', 'unique:android_apis,name'],
			'password' => 'required|confirmed',
			'status'   => 'required|in:1,2'
		], [
			'name.unique' => 'This name is taken by a user, please try another name',
		]);
		
    	AndroidApi::create([
    		'name' 			=> $request->input('name'),
            'admin_id' 		=> auth()->guard('admin')->user()->id,
            'show_password' => $request->input('password'),
    		'password' 		=> Hash::make($request->input('password')),
    		'status' 		=> $request->input('status'),
    	]);

    	$notify[] = ['success', 'New Android Gateway has been created'];
    	return back()->withNotify($notify);
    }

    public function update(Request $request) {

    	$request->validate([
			'name'     => ['required', 'unique:android_apis,name,' . request()->id],
			'password' => 'required',
			'status'   => 'required|in:1,2'
		], [
			'name.unique' => 'This name is taken by a user, please try another name',
		]);

    	$androidApi = AndroidApi::where('admin_id', auth()->guard('admin')->user()->id)->where('id', $request->input('id'))->firstOrFail();
    	$androidApi->update([
    		'name' 			=> $request->input('name'),
            'admin_id' 		=> auth()->guard('admin')->user()->id,
            'show_password' => $request->input('password'),
    		'password' 		=> Hash::make($request->input('password')),
    		'status' 		=> $request->input('status'),
    	]);

    	$notify[] = ['success', 'Android Gateway has been updated'];
    	return back()->withNotify($notify);
    }

    public function simList($id) {

    	$android  = AndroidApi::where('admin_id', auth()->guard('admin')->user()->id)->firstOrFail();
    	$title    = ucfirst($android->name)." api gateway sim list";
    	$simLists = AndroidApiSimInfo::where('android_gateway_id', $id)->latest()->with('androidGatewayName')->paginate(paginateNumber());
    	return view('admin.android.sim', compact('title', 'android', 'simLists'));
    }

    public function delete(Request $request) {

        $android = AndroidApi::where('admin_id', auth()->guard('admin')->user()->id)->where('id', $request->input('id'))->firstOrFail();
        $android->simInfo()->delete();
        $android->delete();
        $notify[] = ['success', 'Android Gateway has been deleted'];
        return back()->withNotify($notify);
    }

    public function simNumberDelete(Request $request) {

        AndroidApiSimInfo::where('id', $request->id)->delete();
        $notify[] = ['success', 'Android Gateway sim has been deleted'];
        return back()->withNotify($notify);
    }

	public function linkStore(Request $request) {

		$request->validate([
			'app_link'     => ['required', 'url'],
			
		]);

		$general = GeneralSetting::first();
		$general->app_link = $request->input("app_link");
		$general->save();
		$notify[] = ['success', 'Apk file link added'];
        return back()->withNotify($notify);
	}
}
