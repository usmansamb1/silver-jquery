<?php
// app/Mail/TestMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $body = 'This is a test email from the Jerei platform to confirm that your email configuration is working properly.';
        return $this->from('usman.nawaz@aljeri.com', 'Jerei System')
                    ->subject('Jerei System Test Email')
                    ->view('admin.test-email-mail', compact('body'));
    }
} 