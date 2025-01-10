<?php

namespace App\Jobs;

use App\Models\User\User;
use App\Notifications\LateProductsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LateProductsReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $filePath;

    protected $user ;
    public function __construct(User $user,$filePath)
    {
        $this->user = $user;
        $this->filePath = $filePath ;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $sales_managers = User::role('sales manager')->get();
        foreach($sales_managers as $sales_manager){
            new LateProductsReportJob($sales_manager,'');
        }
        $this->user->notify(new LateProductsNotification('filePath.xlsx'));
    }
}
