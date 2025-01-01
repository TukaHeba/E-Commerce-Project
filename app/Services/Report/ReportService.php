<?php

namespace App\Services\Report;


use App\Models\User\User;
use App\Jobs\SendUnsoldProductEmail;
use Illuminate\Support\Facades\Artisan;

class ReportService
{
    /**
     * Orders late to deliver report
     */
    public function repor1()
    {
        //
    }

    /**
     * Products remaining in the cart without being ordered report
     */
    public function repor2()
    {
        //
    }

    /**
     * Products running low on the stock report
     */
    public function repor3()
    {
        //
    }

    /**
     * Best-selling products for offers report
     */
    public function repor4()
    {
        //
    }

    /**
     * Best categories report
     */
    public function repor5()
    {
        //
    }

    /**
     * The country with the highest number of orders report
     */
    public function repor6()
    {
        //
    }
/**
     * The products never been sold
     */
    public function sendUnsoldProductsEmail()
    {
        // Fetch all users with the role 'sales manager'
        $user = User::role('sales manager')->first();
        // Dispatch the job for each user and collect the results
            $job = new SendUnsoldProductEmail($user);
            $job->handle(); // Execute the job synchronously
            $result = $job->getUnsoldProducts(); // Get the result
        return $result;
    }



}
