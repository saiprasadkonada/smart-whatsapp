<?php
namespace App\Http\Utility;

use App\Models\CampaignContact;
use App\Service\SmsService;
use Textmagic\Services\TextmagicRestClient;
use Twilio\Rest\Client;
use App\Models\SMSlog;
use App\Models\CreditLog;
use App\Models\User;
use Illuminate\Support\Str;
use GuzzleHttp\Client AS InfoClient;
use Infobip\Api\SendSmsApi;
use Infobip\Configuration;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use App\Models\GeneralSetting;
use Exception;


class SendSMS {

    public static function nexmo($to,$datacoding,$message,$credential,$smsId): void {

        $log = SMSlog::find($smsId);
		
        try {
            $basic 	  = new \Vonage\Client\Credentials\Basic($credential->api_key, $credential->api_secret);
            $client   = new \Vonage\Client($basic);
            $response = $client->sms()->send(
                new \Vonage\SMS\Message\SMS($to, $credential->sender_id, $message)
            );
            $message = $response->current();
            $status  = "Fail";

            if($message->getStatus() == 0) {
                $status = "Success";
            }

            SmsService::updateSMSLogAndCredit($log, $status);

        } catch (\Exception $e) {

            SmsService::updateSMSLogAndCredit($log, 'Fail', $e->getMessage());
        }
    }

	public static function twilio($to,$datacoding,$message,$credential,$smsId): void {

        $log = SMSlog::find($smsId);
        try {

            $twilioNumber = $credential->from_number;
            $client 	  = new Client($credential->account_sid, $credential->auth_token);
            $client->messages->create('+'.$to, [
                'from' => $twilioNumber,
                'body' => $message
            ]);

            $status 	  = "Success";
            SmsService::updateSMSLogAndCredit($log, $status);

        } catch (\Exception $e) {

            $status = "Fail";
            SmsService::updateSMSLogAndCredit($log, $status, $e->getMessage());
        }
	}

	public static function messageBird($to,$datacoding,$message,$credential, $smsId): void {

		$log = SMSlog::find($smsId);
		try {
			$MessageBird 		 = new \MessageBird\Client($credential->access_key);
			$Message 			 = new \MessageBird\Objects\Message();
			$Message->originator = $credential->sender_id;
			$Message->recipients = array($to);
			$Message->datacoding = $datacoding;
			$Message->body 		 = $message;
			$MessageBird->messages->create($Message);

            $status = "Success";
            SmsService::updateSMSLogAndCredit($log, $status);

		} catch (\Exception $e) {
            $status = "Fail";
            SmsService::updateSMSLogAndCredit($log, $status, $e->getMessage());
		}
	}

	public static function textMagic($to,$datacoding,$message,$credential, $smsId): void {

		$log 	= SMSlog::find($smsId);
		$client = new TextmagicRestClient($credential->text_magic_username, $credential->api_key);
		try {

		    $client->messages->create(
		        array(
		            'text' 	 => $message,
		            'phones' => $to,
		        )
		    );

            $status = "Success";
            SmsService::updateSMSLogAndCredit($log, $status);
		} catch (\Exception $e) {

            $status = "Fail";
            SmsService::updateSMSLogAndCredit($log, $status, $e->getMessage());
		}
	}

	public static function clickaTell($to,$datacoding,$message,$credentials,$smsId): void {

		$log = SMSlog::find($smsId);
		try {

			$message  = urlencode($message);
			$to 	  = urlencode($to);
			$key 	  = ($credentials->clickatell_api_key);
			$response = @file_get_contents("https://platform.clickatell.com/messages/http/send?apiKey=$key&to=$to&content=$message");
			if ($response==false) {

                $status = "Fail";
                SmsService::updateSMSLogAndCredit($log, $status,"API Error, Check Your Settings");

			} else {

                $status = "Success";
                SmsService::updateSMSLogAndCredit($log, $status);
			}

		} catch (\Throwable $e) {

            $status = "Fail";
            SmsService::updateSMSLogAndCredit($log, $status,$e->getMessage());
		}
	}

	public static function infoBip($to,$datacoding,$message,$credentials,$smsId): void {

		$BASE_URL 	   = $credentials->infobip_base_url;
		$API_KEY  	   = $credentials->infobip_api_key;
		$SENDER   	   = $credentials->sender_id;
		$RECIPIENT 	   = $to;
		$MESSAGE_TEXT  = $message;
		$configuration = (new Configuration())
		    ->setHost($BASE_URL)
		    ->setApiKeyPrefix('Authorization', 'App')
		    ->setApiKey('Authorization', $API_KEY);

		$client 	 = new InfoClient();
		$sendSmsApi  = new SendSMSApi($client, $configuration);
		$destination = (new SmsDestination())->setTo($RECIPIENT);
		$message 	 = (new SmsTextualMessage())
		    ->setFrom($SENDER)
		    ->setText($MESSAGE_TEXT)
		    ->setDestinations([$destination]);

		$request = (new SmsAdvancedTextualRequest())->setMessages([$message]);
		$log 	 = SMSlog::find($smsId);
		try {

		    $sendSmsApi->sendSmsMessage($request);
            $status = "Success";
            SmsService::updateSMSLogAndCredit($log, $status);
		} catch (\Throwable $apiException) {
	
            $status = "Fail";
            SmsService::updateSMSLogAndCredit($log, $status, "something is wrong");
		}
	}

	public static function smsBroadcast($to,$datacoding,$message,$credentials,$smsId): void {

		$log = SMSlog::find($smsId);
		try {

			$message = urlencode($message);
			$result  = @file_get_contents("https://api.smsbroadcast.com.au/api-adv.php?username=$credentials->sms_broadcast_username&password=$credentials->sms_broadcast_password&to=$to&from=$credentials->sender_id,&message=$message&ref=112233&maxsplit=5&delay=15");
            $status  = "Success";

			if ($result==Str::contains($result, 'ERROR:') || $result==Str::contains($result, 'BAD:')) {

                $status = "Fail";
			}
            SmsService::updateSMSLogAndCredit($log, $status);

		} catch (\Throwable $e) {

            $status = "Fail";
            SmsService::updateSMSLogAndCredit($log, $status, $e->getMessage());
		}
	}

	public static function mimSMS($to,$datacoding,$message,$credentials,$smsId): void {

		$log = SMSlog::find($smsId);
		try {

			$message = $log->sms_type=='1'?rawurlencode($message):$message;
			$url 	 = $credentials->api_url;
		  	$data = [
			    "api_key"  => $credentials->api_key,
			    "type" 	   => $datacoding,
			    "contacts" => $to,
			    "senderid" => $credentials->sender_id,
			    "msg" 	   => $message,
		  	];
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($ch);
			curl_close($ch);
			
			if ($response =='1002' || $response =='1003' || $response =='1004' || $response =='1005' || $response =='1006' || $response=='1007' || $response=='1008' || $response=='1009' || $response=='1010' || $response=='1011') {
                $status = "Fail";
                SmsService::updateSMSLogAndCredit($log, $status, $response);
			} else {

                $status = "Success";
                SmsService::updateSMSLogAndCredit($log, $status, json_encode($data));
			}
		} catch (Exception $e) {

            $status = "Fail";
            SmsService::updateSMSLogAndCredit($log, $status, $e->getMessage());
		}
	}

	public static function ajuraSMS($to,$datacoding,$message,$credentials,$smsId): void {

		$log = SMSlog::find($smsId);

		try {

			$message = urlencode($message);
			$url 	 = $credentials->api_url;
           	$result  = @file_get_contents("https://smpp.ajuratech.com:7790/sendtext?apikey=$credentials->api_key&secretkey=$credentials->secret_key&callerID=$credentials->sender_id&toUser=$to&messageContent=$message");
           	$result  = json_decode($result);

			if ($result->Status=='0') {

                $status = "Success";
                SmsService::updateSMSLogAndCredit($log, $status);
			} else {

                $status = "Fail";
                SmsService::updateSMSLogAndCredit($log, $status, $result->Text);
			}
		} catch (Exception $e) {

            $status = "Fail";
            SmsService::updateSMSLogAndCredit($log, $status, $e->getMessage());
		}
	}

	public static function msg91($to,$datacoding,$message,$credentials,$smsId): void {

		$log 		= SMSlog::find($smsId);
		$unicode 	= $datacoding == "plain" ? 0 : 1;
		$recipients = array(
		    array(
		        "mobiles" => $to,
		        "VAR1" => $message
		    )
		);

		//Prepare you post parameters
		$postData = array(
		    "sender" 	 => $credentials->sender_id,
		    "flow_id" 	 => $credentials->flow_id,
		    "recipients" => $recipients,
		    "unicode" 	 => $unicode
		);
		$postDataJson = json_encode($postData);
		$url 		  = $credentials->api_url;

		try {

			$curl = curl_init();
			curl_setopt_array($curl, array(
			    CURLOPT_URL 		   => "$url",
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_CUSTOMREQUEST  => "POST",
			    CURLOPT_POSTFIELDS 	   => $postDataJson,
			    CURLOPT_HTTPHEADER     => array(
			        "authkey: $credentials->auth_key",
			        "content-type: application/json"
			    ),
			));
			$response = curl_exec($curl);
			$err 	  = curl_error($curl);
			curl_close($curl);
            $status  = "Fail";
            $message = '';
			
			if ($err) {

                $message = "cURL Error #: " . $err;
			}
			
            if ($response != false && json_decode($response)->type=="success") {
				
                $status  = "Success";
                $message = json_decode($response)->type;

            } else {
				
                $status  = "Fail";
                $message = $response == false ? $message : "Failed #: " . json_decode($response)->message;
            }
            SmsService::updateSMSLogAndCredit($log, $status, $message);

		} catch (Exception $e) {
			
            SmsService::updateSMSLogAndCredit($log, $status, $e->getMessage());
		}
	}
}
