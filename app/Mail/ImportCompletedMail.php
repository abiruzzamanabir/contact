<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ImportCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $ip;
    public $time;

    public function __construct($user, $ip, $time)
    {
        $this->user = $user;
        $this->ip = $ip;
        $this->time = $time;
    }

    public function build()
    {
        return $this->subject('Import Completed')
                    ->view('emails.import-completed'); // ✅ Must match your saved Blade file
    }
}
