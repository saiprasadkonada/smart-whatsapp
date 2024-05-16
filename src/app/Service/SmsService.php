<?php

namespace App\Service;

use Carbon\Carbon;
use App\Models\User;
use App\Models\SMSlog;
use App\Models\Contact;
use App\Models\Gateway;
use App\Jobs\ProcessSms;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Utility\SendSMS;
use App\Models\GeneralSetting;
use App\Models\CampaignContact;
use App\Models\AndroidApiSimInfo;
use App\Http\Requests\StoreSMSRequest;
use App\Http\Utility\SendMail;
use Illuminate\Database\Eloquent\Builder;

class SmsService {

    public CustomerService $customerService;

    /**
     * Constructor for initializing CustomerService dependency.
     *
     * @param CustomerService $customerService The customer service instance.
     */
    public function __construct (CustomerService $customerService) {

        $this->customerService = $customerService;
    }

    /**
     * Sends an SMS using the configured gateway associated with the SMS log.
     *
     * @param mixed $smsLog The SMS log instance containing the message details.
     * @param int|null $diffInSeconds Optional delay in seconds before dispatching the SMS.
     * @return void
     */
    public function sendSmsByOwnGateway($smsLog, $diffInSeconds = null, $contactCount = null):void {
        
        if (is_null($smsLog->android_gateway_sim_id) && !is_null($smsLog->api_gateway_id)) {
            
            $smsGateway = Gateway::where('id', $smsLog->api_gateway_id)->first();
            $creds      = $smsGateway->sms_gateways;

            if ($smsGateway && $contactCount > 1) {
                 
                ProcessSms::dispatch($smsLog, (array)$creds, $smsGateway)->delay(now()->addSeconds($diffInSeconds));
            } else {

                try {
                        
                    $smsLog->api_gateway_id         = $smsGateway->id;
                    $smsLog->android_gateway_sim_id = null;
                    $smsType                        = $smsLog->sms_type == 1 ? 'plain' : 'unicode';
                    $gateways = [
                        "101NEXMO"         => 'nexmo',
                        "102TWILIO"        => 'twilio',
                        "103MESSAGE_BIRD"  => 'messageBird',
                        "104TEXT_MAGIC"    => 'textMagic',
                        "105CLICKA_TELL"   => 'clickaTell',
                        "106INFOBIP"       => 'infoBip',
                        "107SMS_BROADCAST" => 'smsBroadcast',
                        "108MIM_SMS"       => 'mimSMS',
                        "109AJURA_SMS"     => 'ajuraSMS',
                        "110MSG91"         => 'msg91'
                    ];
                    
                    if (isset($gateways[$smsGateway->type])) {

                        $gateway = $gateways[$smsGateway->type];
                        SendSMS::$gateway($smsLog->to, $smsType, $smsLog->message, (object)$creds, $smsLog->id);
                    }
                  
                } catch (\Exception $exception) { }
            }
        }
	}

    /**
     * Searches SMS logs based on provided criteria.
     *
     * @param string|null $search The search query for user name or phone number.
     * @param string|null $searchDate The date range in format 'mm/dd/yyyy - mm/dd/yyyy'.
     * @return Builder Query builder for SMS logs.
     */
    public function searchSmsLog($search, $searchDate): Builder {

        $smsLogs = SMSlog::query();

        if (!empty($search)) {

            $smsLogs->whereHas('user',function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")->orWhere('to', 'like', "%$search%");
            });
        }

        if (!empty(request()->input('status'))) {

            $status = match (request()->input('status')) {

                'pending'    => [1],
                'schedule'   => [2],
                'fail'       => [3],
                'delivered'  => [4],
                'processing' => [5],
                default      => [1,2,3,4,5],
            };
            $smsLogs->whereIn('status',$status);
        }

        if (!empty($searchDate)) {

            $dateRange = explode('-', $searchDate);
            $firstDate = Carbon::createFromFormat('m/d/Y', trim($dateRange[0]))->startOfDay();
            $lastDate  = isset($dateRange[1]) ? Carbon::createFromFormat('m/d/Y', trim($dateRange[1]))->endOfDay() : null;
            if ($firstDate) {

                $smsLogs->whereDate('created_at', '>=', $firstDate);
            }
            if ($lastDate) {

                $smsLogs->whereDate('created_at', '<=', $lastDate);
            }
        }

        return $smsLogs;
    }

    /**
     * Updates status of SMS logs and processes them accordingly.
     *
     * @param int $status The new status for the SMS logs.
     * @param array $smsLogIds The IDs of the SMS logs to be updated.
     * @param GeneralSetting $general The general settings instance.
     * @param int|null $sim_id The ID of the SMS for which the status is being updated (if applicable).
     * @return void
     */
	public function smsLogStatusUpdate(int $status, array $smsLogIds, GeneralSetting $general, ?int $sim_id):void {
        
        $general = GeneralSetting::first();
       
		foreach (array_reverse($smsLogIds) as $smsLogId) {

			$smslog = SMSlog::find($smsLogId);

			if (!$smslog) { 
                
                continue; 
            }

            $user       = User::find($smslog->user_id);
            $wordLength = $smslog->sms_type == "plain" ? $general->sms_word_text_count : $general->sms_word_unicode_count;
            
            if ($status == SMSlog::PENDING && $user) {
                
                $messages    = str_split($smslog->message,$wordLength);
                $totalCredit = count($messages);
                
                if ($user->credit >= $totalCredit) {

                    $smslog->status = $status;
                    if($smslog->api_gateway_id) {

                        ProcessSms::dispatch($smslog, (array)$smslog->smsGateway()->first()->sms_gateways, $smslog->smsGateway()->first());
                    }
                    $this->customerService->deductCreditAndLogTransaction($user, $totalCredit, 1);

                } else {
                    $mailCode = [
                        'type'           => "sms",
                        'name'           => $smslog->to,
                        'credit_balance' => $user->credit,
                    ];
                    SendMail::MailNotification($user,'INSUFFICIENT_CREDIT',$mailCode);
                }
            } else {

                $smslog->status = $status;
                if ($sim_id) {

                    $sim_number                     = AndroidApiSimInfo::where("id", $sim_id)->value("sim_number");
                    $smslog->api_gateway_id         = null;
                    $smslog->android_gateway_sim_id = $sim_id;
                    $smslog->sim_number             = $sim_number;
                    $smslog->save();
                } elseif(!$smslog->api_gateway_id && !$sim_id) {
                    
                    $smslog->android_gateway_sim_id = null;
                    $smslog->save();
                }
                if($smslog->api_gateway_id) {
                    ProcessSms::dispatch($smslog, (array)$smslog->smsGateway()->first()->sms_gateways, $smslog->smsGateway()->first());
                } 
            } //send one 
			$smslog->update();
		}
	}

    /**
     * @param Request $request
     * @param array $allContactNumber
     * @return void
     */
    public function processNumber(Request $request, array &$allContactNumber): void {
        
        if ($request->has('number')) {
            $contactNumber       = preg_replace('/[ ,]+/', ',', trim($request->input('number')));
            $allContactNumber[]  = explode(",",$contactNumber);
        }
    }

    /**
     * @param Request $request
     * @param array $allContactNumber
     * @param array $numberGroupName
     * @param null $userId
     * @return void
     */
    public function processGroupId(Request $request, array &$allContactNumber, array &$numberGroupName, $userId = null): void {

        if ($request->has('group_id')) {
           
            $contact = Contact::query();
            $contact->whereIn('group_id', $request->input('group_id'));

            if ($request->input("group_logic")) {

                $logic         = $request->input("logic");
                $attributeName = $request->input("attribute_name");
            
                if (strpos($attributeName, "::") !== false) {

                    $attributeParts = explode("::", $attributeName);
                    $attributeType  = $attributeParts[1];
                    
                    if ($attributeType == GeneralSetting::DATE) {

                        $startDate = Carbon::parse($logic);
            
                        if ($request->has('logic_range')) {

                            $endDate = Carbon::parse($request->input('logic_range'));
                            $contact = $contact->get()->filter(function ($contact) use ($startDate, $endDate, $attributeParts) {

                                $attr = Carbon::parse($contact->attributes->{$attributeParts[0]}->value);
                                return $attr->between($startDate, $endDate);
                            });
                        } else {

                            $contact = $contact->get()->filter(function ($contact) use ($startDate, $attributeParts) {

                                $attr = Carbon::parse($contact->attributes->{$attributeParts[0]}->value);
                                return $attr->isSameDay($startDate);
                            });
                        }
                    } elseif ($attributeType == GeneralSetting::BOOLEAN) {

                        $logicValue = filter_var($logic, FILTER_VALIDATE_BOOLEAN);
                        $contact    = $contact->get()->filter(function ($contact) use ($attributeParts, $logicValue) {

                            $attrValue = filter_var($contact->attributes->{$attributeParts[0]}->value, FILTER_VALIDATE_BOOLEAN);
                            return $attrValue === $logicValue;
                        });

                    } elseif ($attributeType == GeneralSetting::NUMBER) { 

                        $numericLogic = filter_var($logic, FILTER_VALIDATE_FLOAT);
                    
                        if ($request->has('logic_range')) {

                            $numericRange = filter_var($request->input('logic_range'), FILTER_VALIDATE_FLOAT);
                            $contact      = $contact->get()->filter(function ($contact) use ($attributeParts, $numericLogic, $numericRange) {

                                $attrValue = filter_var($contact->attributes->{$attributeParts[0]}->value, FILTER_VALIDATE_FLOAT);
                                return $attrValue >= $numericLogic && $attrValue <= $numericRange;
                            });
                        } else {

                            $contact = $contact->get()->filter(function ($contact) use ($attributeParts, $numericLogic) {

                                $attrValue = filter_var($contact->attributes->{$attributeParts[0]}->value, FILTER_VALIDATE_FLOAT);
                                return $attrValue == $numericLogic;
                            });
                        }
                    } elseif ($attributeType == GeneralSetting::TEXT) { 

                        $textLogic = $request->input('logic');
                        $contact   = $contact->get()->filter(function ($contact) use ($attributeParts, $textLogic) {

                            $attrValue = $contact->attributes->{$attributeParts[0]}->value;
                            return stripos($attrValue, $textLogic) !== false;
                        });
                    }
                } else {
                    $contact->where($attributeName, 'like', "%$logic%");
                }
            }
            
            if (!is_null($userId)) {

                $contact->where('user_id', $userId);
            } else {

                $contact->whereNull('user_id');
            }
            if ($request->has("channel")) {

                $allContactNumber[] = $contact->pluck("$request->channel".'_contact')->toArray();
                $numberGroupName    = $contact->pluck('first_name', "$request->channel".'_contact')->toArray();
                
            }
        }
    }

    /**
     * @param Request $request
     * @param array $allContactNumber
     * @param array $numberGroupName
     * @return void
     */
    public function processFile(Request $request, array &$allContactNumber, array &$numberGroupName): void {
       
        if ($request->has('file')) {

            $service   = new FileProcessService();
            $extension = strtolower($request->file('file')->getClientOriginalExtension());
            
            if ($extension == "csv") {

                $response           = $service->processCsv($request->file('file'));
                $allContactNumber[] = array_keys($response);
                $numberGroupName    = $numberGroupName + $response;
            }

            if ($extension == "xlsx") {
                
                $response =  $service->processExel($request->file('file'));
                $allContactNumber[] = array_keys($response);
                $numberGroupName = $numberGroupName + $response;
            }
        }
    }

    /**
     * @param $allContactNumber
     * @return array
     */
    public function flattenAndUnique($allContactNumber): array {

        $contactNewArray = [];
        foreach ($allContactNumber as $childArray) {

            foreach ($childArray as $value) {

                $contactNewArray[] = $value;
            }
        }
        $filtered = Arr::where($contactNewArray, function (string|int $value, int $key) {

            return $value !== "" && filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        });
        return array_unique($filtered);
    }

    /**
     * @param string $value
     * @param array $numberGroupName
     * @param string $message
     * @return string
     */
    public function getFinalContent(mixed $value, array $numberGroupName, string $message): string {
        
        
        $finalContent = textSpinner(str_replace("{{first_name}}",$value, offensiveMsgBlock($message)));
        
        if (array_key_exists($value,$numberGroupName)) {

            $finalContent = str_replace('{{first_name}}', $numberGroupName ? $numberGroupName[$value]:$value, offensiveMsgBlock($message));
        }
        return $finalContent;
    }

    /**
     * @param string $contact
     * @param StoreSMSRequest $request
     * @param array $numberGroupName
     * @param int|null $apiGatewayId
     * @param int|null $userId
     * @return array
     */
    public function prepParams(string $contact, StoreSMSRequest $request, array $numberGroupName, ?int $apiGatewayId, ?int $simId, ?int $userId = null): array {   
       
        $generalSetting = GeneralSetting::first();
        $value          = array_key_exists($contact, $numberGroupName) ? $numberGroupName[$contact] ?? translate("User") : $contact;
        $setTimeInDelay = $request->input('schedule') == 2 ? $request->input('schedule_date') : Carbon::now();
        $wordLength     = $request->input('smsType') == "plain" ? $generalSetting->sms_word_text_count : $generalSetting->sms_word_unicode_count;
        $finalContent   = $this->getFinalContent($value, $numberGroupName, $request->input('message'));
        
        if($simId) {

            $sim_number = AndroidApiSimInfo::where("id", $simId)->value("sim_number");
        }
        return  [
            'to'                     => $contact,
            'word_length'            => $wordLength,
            'user_id'                => $userId,
            'sms_type'               => $request->input('sms_type') == "plain" ? 1 : 2,
            'initiated_time'         => $setTimeInDelay,
            'message'                => $finalContent,
            'status'                 => $request->input('schedule') == 2 ? 2 : SMSlog::PENDING,
            'schedule_status'        => $request->input('schedule'),
            'api_gateway_id'         => $apiGatewayId,
            'android_gateway_sim_id' => $simId,
            'sim_number'             => $sim_number ? $sim_number : null
        ];
    }

    /**
     * @param array $params
     * @return SMSlog
     */
    public function saveSMSLog(array $params): SMSlog {

        return SMSlog::create([
            'to'                     => $params['to'],
            'word_length'            => $params['word_length'],
            'user_id'                => $params['user_id'],
            'sms_type'               => $params['sms_type'],
            'initiated_time'         => $params['initiated_time'],
            'message'                => $params['message'],
            'status'                 => $params['status'],
            'schedule_status'        => $params['schedule_status'],
            'api_gateway_id'         => $params['api_gateway_id'],
            'android_gateway_sim_id' => $params['android_gateway_sim_id'],
            'sim_number'             => $params['sim_number'],
        ]);
    }

    /**
     * @param array $contactNewArray
     * @param GeneralSetting $general
     * @param gateway $smsGateway
     * @param StoreSMSRequest $request
     * @param array $numberGroupName
     * @param array $allAvailableSims
     * @param int $userId
     * @return void
     */
    public function sendSMS(array $contactNewArray, GeneralSetting $general, $smsGateway, StoreSMSRequest $request, array $numberGroupName, array $allAvailableSims, ?int $userId = null): void {
        
        $apiGatewayId = null;

        if ($userId ? auth()->user()->sms_gateway == 1 : $general->sms_gateway == 1) {
           
            $apiGatewayId = (int) $smsGateway->id;

            foreach ($contactNewArray as $value) {

                $log = $this->saveSMSLog($this->prepParams((string)$value, $request, $numberGroupName, $apiGatewayId, null, $userId));
                if ($log->status == 1) {

                    $this->sendSmsByOwnGateway($log, null, count($contactNewArray));
                }
            }

        } else {
            
            if($request->input("android_gateways_id") == "-1") {
                
                foreach ($contactNewArray as $index_key => $number) {
                    
                    foreach ($allAvailableSims as $key => $sim_id){
                      
                        unset($allAvailableSims[$key]);
                        
                        if(empty($allAvailableSims)) {
                            
                            $allAvailableSims = AndroidApiSimInfo::where("status", AndroidApiSimInfo::ACTIVE)->pluck("id")->toArray();
                        }
                        break;
                    }
                    $log = $this->saveSMSLog($this->prepParams((string)$number, $request, $numberGroupName, null, $sim_id, $userId));
                }
                
            } else {

                foreach ($contactNewArray as $value) {

                    $log = $this->saveSMSLog($this->prepParams((string)$value, $request, $numberGroupName, null, $request->sim_id, $userId));
                }
            }
        }
    }

    /**
     * @param SMSlog $log
     * @param $status
     * @param $errorMessage
     * @return void
     */
    public static function updateSMSLogAndCredit(SMSlog $log, $status, $errorMessage = null): void {
        
        $log->status           = $status == 'Success' ? SMSlog::SUCCESS : SMSlog::FAILED;
        $log->delivered_at     = $status == 'Success' ? now() : null;
        $log->response_gateway = !is_null($errorMessage) ? $errorMessage : null;
        $log->save();

        $user = User::find($log->user_id);

        if ($user && $status == 'Fail') {

            $messages    = str_split($log->message,$log->word_length);
            $totalcredit = count($messages);
            CustomerService::addedCreditLog($user, $totalcredit, $log->to);
        }

        if ($log->contact_id) {

            CampaignContact::where('id',$log->contact_id)->update([
                "status" => $status,
            ]);
        }
    }
}
