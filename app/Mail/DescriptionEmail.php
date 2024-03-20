<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DescriptionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $description;
    public $pdfContent;
    public $subject;

    public function __construct($description,$pdfContent)
    {
        $this->description = $description;
        $this->pdfContent = $pdfContent;
        $this->subject($subject);
    }

    public function build()
    {
        return $this->view('email.pdf')
        ->attachData($this->pdfContent, 'document.pdf', [
            'mime' => 'application/pdf',
        ]);
    }
}
