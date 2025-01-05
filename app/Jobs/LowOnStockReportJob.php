<?php

namespace App\Jobs;

use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\LowOnStockNotification;

class LowOnStockReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
     protected $filePath;
    /**
     * Create a new job instance.
     */
    public function __construct(User $user , $filePath)  
    {
        $this->user = $user;
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user->notify(new LowOnStockNotification($this->filePath));  
    }
}
