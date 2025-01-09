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
    protected $file;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user,$file)
    {
        $this->user = $user;
        $this->file = $file;

    }

    /**
     * Execute the job.
     */public function handle(): void
    {
        $this->user->notify(new UnsoldProductNotification($this->file));

    }

}

