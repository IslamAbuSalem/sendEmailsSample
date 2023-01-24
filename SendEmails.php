<?php

require 'vendor/autoload.php';

use Aws\Ses\SesClient;

$emails =[];
//fetch the emails list from BD let's say mailing list table
//$mailingListModel = new Model_MailingList();
//$emails = $mailingListModel->getAll();

$subject = "Test Bulk Email";
$body = "This is a test bulk email.";
$from = "sender@example.com";
$headers = "From: ".$from. "\r\n" ;

/*First Approch send emails using mail function php one by one */
foreach($emails as $email){
    mail($email, $subject, $body, $headers);
}

/* the second approach split the emails list into chunks each containing 50 items, and use Amazone to send these chunks*/
$chunk_size = 50;
$chunks = array_chunk($emails, $chunk_size);

$client = new SesClient([
    'version' => 'latest',
    'region'  => 'us-west-2',
    'credentials' => [
        'key'    => 'YOUR_ACCESS_KEY',
        'secret' => 'YOUR_SECRET_KEY',
    ],
]);


foreach ($chunks as $chunk){
    $result = $client->sendBulkTemplatedEmail([
        'Source' => $from,
        'Template' => 'MyTemplate',
        'Destination' => [
            'ToAddresses' => $chunk,
        ],
        'DefaultTemplateData' => json_encode([
            'subject' => $subject,
            'body' => $body,
        ]),
    ]);


    // Check if the request was failed
    if ($result['statusCode'] != 200) {

        print "Error: " . $result['body'] . "\n";
        ///here we can save error log records in DB

    }

}


?>