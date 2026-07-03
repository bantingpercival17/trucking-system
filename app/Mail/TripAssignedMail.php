<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TripAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $trip;
    public $person;
    public $company;

    public function __construct($trip, $person, $company)
    {
        $this->trip = $trip;
        $this->person = $person;
        $this->company = $company;
    }

    public function build()
    {
        return $this->subject("New Trip Assigned - {$this->company}")
                    ->view('emails.trip_assigned');
    }
}