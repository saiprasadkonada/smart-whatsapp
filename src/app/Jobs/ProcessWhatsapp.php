<?php

namespace App\Jobs;

use App\Http\Utility\SendWhatsapp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\WhatsappLog;
use Exception;

class ProcessWhatsapp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected WhatsappLog $whatsappLog){}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $whatsappLog = $this->whatsappLog;

        if($whatsappLog->mode == WhatsappLog::NODE && $whatsappLog->status != WhatsappLog::FAILED) {
            
            SendWhatsapp::sendNodeMessages($whatsappLog, null);
            
        } else {

            SendWhatsapp::sendCloudApiMessages($whatsappLog, null);
        }
    }
}
