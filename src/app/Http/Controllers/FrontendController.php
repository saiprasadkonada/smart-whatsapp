<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Facades\Auth;

class FrontendController extends Controller
{

    public function selectSearch(Request  $request): \Illuminate\Http\JsonResponse
    {
        $searchData = trim($request->term);
        $user = Auth::user();
        $contacts =  $user->emailContact()->where('email_contact','LIKE',  '%' . $searchData. '%')->select('id as id','email_contact as text')->latest()->simplePaginate(10);
        $pages=true;
        if (empty($contacts->nextPageUrl())){
            $pages=false;
        }
        $results = array(
          "results" => $contacts->items(),
          "pagination" => array(
            "more" => $pages
          )
        );
        return response()->json($results);
    }
    
    public function demoImportFilesms() {

        $path    = filePath()['demo']['path'].'/demo.csv';
        $title   = 'demo.csv';
        $headers = [
            'Content-Type'              => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Length'            => filesize($path),
            'Cache-Control'             => 'must-revalidate',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition'       => 'attachment; filename='.$title
        ];
        return response()->download($path, 'demo.csv', $headers);
    }

    public function demoImportFile() {

        $path    = filePath()['demo']['path_email'].'/demo.csv';
        $title   = 'demo.csv';
        $headers = [
            'Content-Type'              => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Length'            => filesize($path),
            'Cache-Control'             => 'must-revalidate',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition'       => 'attachment; filename='.$title
        ];
        return response()->download($path, 'demo.csv', $headers);
    }

    public function demoFileDownloader($extension, $type) {

        try {

            $headers  = $type == "sms" ? ["email_contact", "whatsapp_contact", "last_name"] : ($type == "email" ? ["sms_contact", "whatsapp_contact", "last_name"] : ["sms_contact", "email_contact", "last_name"]);
          
            $general  = GeneralSetting::first();
            $filePath = generateDemoFile($extension, $general, $headers, false);
            return response()->download($filePath);

        } catch (\Exception $e) {

            $notify[] = ['error', 'Something went wrong, File could not generate'];
            return back()->withNotify($notify);
        }
    }

    public function defaultImageCreate($size=null)
    {
        $width = explode('x',$size)[0];
        $height = explode('x',$size)[1];
        $image = imagecreate($width, $height);
        $fontFile = realpath('assets/theme/frontend/fonts') . DIRECTORY_SEPARATOR . 'Poppins-Regular.ttf';
        if($width > 100 && $height > 100){
            $fontSize = 30;
        }else{
            $fontSize = 5;
        }
        $text = $width . 'X' . $height;
        $backgroundcolor = imagecolorallocate($image, 237, 241, 250);
        $textcolor    = imagecolorallocate($image, 107, 111, 130);
        imagefill($image, 0, 0, $textcolor);
        $textsize = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textsize[4] - $textsize[0]);
        $textHeight = abs($textsize[5] - $textsize[1]);
        $xx = ($width - $textWidth) / 2;
        $yy = ($height + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $xx, $yy, $backgroundcolor , $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }


    public function languageChange($id = null)
    {
        $language = Language::where('id', $id)->first();
        if(!$language){
            $lang = 'en';
            $flag = 'us';
        }

        session()->put('flag', $language->flag);
        session()->put('lang', $language->code);
        $notify[] = ['success', 'Language set to '.$language->name];
        return back()->withNotify($notify);
    }

    public function apiDocumentation()
    {
        $title = "API Documentation";
        $layout = null;
        if (Auth::guard('admin')->user()) {
            $layout = "admin.layouts.app";
        }
        if (Auth::user()) {
            $layout = "user.layouts.app";
        }
        return view('api.index', compact('title','layout'));
    }
}
