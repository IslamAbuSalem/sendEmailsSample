<?php
require 'vendor/autoload.php';

use Aws\Ses\SesClient;
use Bernard\Message;

class SendEmailSESJob implements Bernard\Message
{
    private $recipients;
    private $subject;
    private $message;
    private $headers;
    private $client;
    private $from;

    public function __construct($from, $recipients, $subject, $message)
    {
        $this->from = $from;
        $this->recipients = $recipients;
        $this->subject = $subject;
        $this->message = $message;



        $this->client = new SesClient([
            'version' => 'latest',
            'region'  => 'us-west-2',
            'credentials' => [
                'key'    => 'YOUR_ACCESS_KEY', //these keys should be saved in config file
                'secret' => 'YOUR_SECRET_KEY',
            ],
        ]);

    }

    public function handle(Bernard\Queue $queue)
    {

        $result = $this->client->sendBulkTemplatedEmail([
            'Source' => $this->from,
            'Template' => 'MyTemplate',
            'Destination' => [
                'ToAddresses' => $this->recipients,
            ],
            'DefaultTemplateData' => json_encode([
                'subject' => $this->subject,
                'body' => $this->message,
            ]),
        ]);


        // Check if the request was failed
        if ($result['statusCode'] != 200) {

            print "Error: " . $result['body'] . "\n";
            ///here we can save error log records in DB
            
        }

    }
}
