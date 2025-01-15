<?php

namespace App\Jobs;

use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\OrderTrackingNotification;

class SendOrderTrackingEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_email;
    protected $user_first_name;
    protected $order_id;
    protected $order_status;

    /**
     * Create a new job instance.
     */
    public function __construct($user_email, $user_first_name, $order_id, $order_status)
    {
        $this->user_email = $user_email;
        $this->user_first_name = $user_first_name;
        $this->order_id = $order_id;
        $this->order_status = $order_status;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::where('email', $this->user_email)->first();
        $user->notify(new OrderTrackingNotification($this->user_email, $this->user_first_name, $this->order_id, $this->order_status));
    }
}
