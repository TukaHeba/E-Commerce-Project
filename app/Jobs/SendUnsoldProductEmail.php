<?php

namespace App\Jobs;

use App\Models\User\User;
use Illuminate\Bus\Queueable;
use App\Services\Report\ReportService;
use Illuminate\Queue\SerializesModels;
use App\Http\Resources\ProductResource;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\UnsoldProductNotification;

class SendUnsoldProductEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
    protected $filePath;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user,$filePath)
    {
        $this->user = $user;
        $this->filePath = $filePath;

    }

    /**
     * Execute the job.
     */public function handle(): void
    {
        $this->user->notify(new UnsoldProductNotification($this->filePath));

    }

}

