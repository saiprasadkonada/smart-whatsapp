<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\SMSlog;
use App\Enums\StatusEnum;
use App\Models\CreditLog;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Models\CampaignContact;
use Illuminate\Validation\Rule;
use App\Models\AndroidApiSimInfo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Utility\Api\ApiJsonResponse;

class AndroidApiSmsController extends Controller {

    /**
     * @return JsonResponse
     */
    public function init() {

        try {

            return ApiJsonResponse::success("Successfully initiated request.", GeneralSetting::select('site_name')->first());
        
        } catch(\Exception $e) {
            
            return ApiJsonResponse::exception($e);
        }
    }

    /**
     *
     * @param Request $request
     * @return JsonResponse
     * 
     */
    public function configureSim(Request $request) {

        try {

            $validator = Validator::make($request->all(),[
                
                'android_gateway_id' => 'exists:android_apis,id',
                'time_interval'      => 'integer',
                'send_sms'           => 'integer',
                'status'             => [Rule::in([StatusEnum::TRUE->status(), StatusEnum::FALSE->status()])]
            ]);
    
            if ($validator->fails()) {
               
                return ApiJsonResponse::validationError($validator->errors());
            }

            $information = [];

            foreach($request->toArray() as $key => $value) {

                $information[$key] = $value;
            }
    
            $simInfo = AndroidApiSimInfo::updateOrCreate([
                'id' => $request->input("id"),
            ], $information);
            
            $data = [
                'android_gateway_sim_id' => $simInfo->id
            ];

            return ApiJsonResponse::success($simInfo->wasRecentlyCreated ? "Successfully Added a new SIM" 
                                                                            : "SIM Successfully Updated", 
                                            $data, $simInfo->wasRecentlyCreated ? 201 : 200);

        } catch(\Exception $e) {

            return ApiJsonResponse::exception($e);
        }
    }

    /**
     *
     * @param Request $request
     * @return JsonResponse
     * 
     */
    public function smsfind(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
            
                'android_gateway_sim_id' => 'required|exists:android_api_sim_infos,id',
            ], [
                'android_gateway_sim_id.exists' => "Selected sim for this sms is no longer available"
            ]);
    
            if ($validator->fails()) {
    
                return ApiJsonResponse::validationError($validator->errors());
            }
    
            $smslog = SMSlog::whereNull('api_gateway_id')
                                ->where('android_gateway_sim_id',$request->android_gateway_sim_id)
                                ->where('status', 1)
                                ->select('id', 'android_gateway_sim_id','to','initiated_time', 'message')
                                ->take(1)
                                ->first();

            return ApiJsonResponse::success($smslog ? "Successfully Fetched SMS from logs" : '', $smslog ? $smslog->toArray() : null);
            
            

        } catch (\Exception $e) {

            return ApiJsonResponse::exception($e);
        }
    }

    /**
     *
     * @param Request $request
     * @return JsonResponse
     * 
    */
    public function smsStatusUpdate(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                
                'id'     => ['required', 'exists:s_m_slogs,id'],
                'status' => ['required', Rule::in([SMSlog::FAILED, SMSlog::SUCCESS, SMSlog::PROCESSING])],
            ], [
                'id.exists' => "SMS is not longer available"
            ]);
    
            if ($validator->fails()) {
    
                return ApiJsonResponse::validationError($validator->errors());
            }
    
            $smslog = SMSlog::where('id', $request->id)
                                ->whereIn('status', [SMSlog::PENDING, SMSlog::PROCESSING])
                                ->first(); 
            
                            
            if(!$smslog) { return ApiJsonResponse::notFound(); }
    
            if ($smslog) {
    
                if ($request->status == SMSlog::SUCCESS) {
                    
                    $smslog->status       = SMSlog::SUCCESS;
                    $smslog->delivered_at = now();
                    $smslog->save();
    
                    if($smslog->contact_id) {
    
                        $this->updateContact($smslog->contact_id, "Success");
                    }
                   
                    
                } elseif ($request->status == SMSlog::PROCESSING) {
    
                    $smslog->status = SMSlog::PROCESSING;
                    $smslog->save();
                    if($smslog->contact_id) {
    
                        $this->updateContact($smslog->contact_id, "Processing");
                    }
                } else {
                    
                    $smslog->response_gateway = $request->response_gateway;
                    $smslog->status           = SMSlog::FAILED;
                    $smslog->save();

                    if($smslog->user_id) {
                        
                        $messages      = str_split($smslog->message,160); 
                        $totalcredit   = count($messages);
                        $user          = User::find($smslog->user_id);
                        $user->credit += $totalcredit;
                        $user->save();
    
                        $creditInfo              = new CreditLog();
                        $creditInfo->user_id     = $smslog->user_id;
                        $creditInfo->credit_type = "+";
                        $creditInfo->credit      = $totalcredit;
                        $creditInfo->trx_number  = trxNumber();
                        $creditInfo->post_credit = $user->credit;
                        $creditInfo->details     = $totalcredit." Credit Return ".$smslog->to." is Falied";
                        $creditInfo->save();
                    }
                    if($smslog->contact_id) {

                        $this->updateContact($smslog->contact_id, "Fail");
                    }
                }
            }

           
            return ApiJsonResponse::success("Successfully updated sms status.");

        } catch(\Exception $e) {

            return ApiJsonResponse::exception($e);
        }
    }

    /**
     *
     * @param int $id
     * @param string $status
     * @return JsonResponse
     * 
    */
    public function updateContact(int $id, string $status) {

        $campaign_contact = CampaignContact::where("id",$id)->first();
        $campaign_contact->status = $status;
        $campaign_contact->save();
    }
}
