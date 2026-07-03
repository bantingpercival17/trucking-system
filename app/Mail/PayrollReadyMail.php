<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayrollReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $person;
    public $rows;
    public $amount;
    public $weekStart;
    public $weekEnd;
    public $company;

    public $lastBalance;
    public $advanceDeducted;
    public $remainingBalance;
    public $netPay;

    public function __construct(
        $person,
        $rows,
        $amount,
        $weekStart,
        $weekEnd,
        $company,
        $lastBalance,
        $advanceDeducted,
        $remainingBalance,
        $netPay
    ) {
        $this->person = $person;
        $this->rows = $rows;
        $this->amount = $amount;
        $this->weekStart = $weekStart;
        $this->weekEnd = $weekEnd;
        $this->company = $company;

        $this->lastBalance = $lastBalance;
        $this->advanceDeducted = $advanceDeducted;
        $this->remainingBalance = $remainingBalance;
        $this->netPay = $netPay;
    }

    public function build()
    {
        return $this->subject("Payroll Ready - {$this->company}")
                    ->view('emails.payroll_ready');
    }
}