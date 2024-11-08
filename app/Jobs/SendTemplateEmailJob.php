<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;


class SendTemplateEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $templateName;
    protected $templateData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($templateName, $templateData)
    {
        $this->templateName = $templateName;
        $this->templateData = $templateData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $emailTemplateData = \DB::table('mst_template')->select('is_active', 'api_url')
            ->where('type', $this->templateName['type'])
            ->where('key', $this->templateName['key'])
            // ->where('is_active', 1)
            ->whereNotNull('api_url')
            ->first();

        $postUrl = $emailTemplateData->api_url ?? null;
        $postData = [];

        if ($this->templateName['key'] == 'forgot_password' && $this->templateName['type'] == 'email') {
            $data = json_decode(json_encode($this->templateData), true);

            $link = array("link" => $data['otpLink']);
            $userData = array_merge($data['user'], $link);

            if (!empty($emailTemplateData)) {
                $replaceData = $this->templateName['payload'];
                $postData = array_replace($replaceData, $userData);
            }
        }

        if ($this->templateName['key'] == 'metric_entry_reminders' && $this->templateName['type'] == 'email') {
            $data = json_decode(json_encode($this->templateData), true);

            $userData = $data;

            if (!empty($emailTemplateData)) {
                $replaceData = $this->templateName['payload'];
                $postData = array_replace($replaceData, $userData);
            }
        }

        if ($this->templateName['key'] == 'new_member_added' && $this->templateName['type'] == 'email') {
            $data = json_decode(json_encode($this->templateData), true);

            $userData = $data;

            if (!empty($emailTemplateData)) {
                $replaceData = $this->templateName['payload'];
                $postData = array_replace($replaceData, $userData);
            }
        }
        if ($postUrl && $postData) {
            $response = Http::post($postUrl, [
                'payload' => $postData,
            ]);
        }
    }
}
