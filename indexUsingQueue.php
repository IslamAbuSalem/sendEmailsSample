<?php

require_once 'vendor/autoload.php';

use Bernard\QueueFactory\PersistentFactory;
use Bernard\Serializer;
use Bernard\Driver\PredisDriver;

// Connect to Redis
$client = new \Predis\Client();
$driver = new PredisDriver($client);
$factory = new PersistentFactory($driver, new Serializer());

// Create a queue
$queue = $factory->create('email');
$emails =[];
//fetch the emails list from BD let's say mailing list table
//$mailingListModel = new Model_MailingList();
//$emails = $mailingListModel->getAll();

$subject = "Test Bulk Email";
$body = "This is a test bulk email.";
$from = "sender@example.com";
$headers = "From: ".$from. "\r\n" ;
/*First approch send emails using mail() function php one by one */
foreach($emails as $email){
    // Create a job
    //we should have SendEmail class that implements handle
    $job = new SendEmailJob($email,$subject, $body, $headers);
    // Add the job to the queue
    $queue->produce($job);
}

/* the second approach split the emails list into chunks each containing 50 items, and use Amazone to send these chunks*/
$chunk_size = 50;
$chunks = array_chunk($emails, $chunk_size);

foreach ($chunks as $chunk){
    // Create a job
    //we should have SendEmail class that implements handle
    $job = new SendEmailSESJob($from, $chunk, $subject,$body);
    // Add the job to the queue
    $queue->produce($job);
}

// Process the jobs in the queue
$worker = new \Bernard\Worker($queue, $factory);
$worker->work();

?>