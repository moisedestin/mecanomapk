<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AutomaticAcceptNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $notification;
    public $destination_token;
    public $notification_array;


    public function __construct(Notification $notification, $destination_token, $notification_array)
    {
        $this->notification = $notification;
        $this->destination_token = $destination_token;
        $this->notification_array = $notification_array;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->notification->pushnotification($this->destination_token,$this->notification_array);
    }
}
