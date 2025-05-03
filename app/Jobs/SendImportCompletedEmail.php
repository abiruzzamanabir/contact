<?php

namespace App\Jobs;

use App\Mail\ImportCompletedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendImportCompletedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $ip;
    protected $time;

    public function __construct($user, $ip, $time)
    {
        $this->user = $user;
        $this->ip = $ip;
        $this->time = $time;
    }

    public function handle()
    {
        \Log::info('Sending import completed email...');
        Mail::to($this->user->email)->send(new ImportCompletedMail($this->user, $this->ip, $this->time));
        \Log::info('Email sent to: ' . $this->user->email);
    }
}
