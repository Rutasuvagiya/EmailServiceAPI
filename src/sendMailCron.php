<?php

/**
 * Cron file to send emails which are in Redis queue
 *
 * should run continuously to process email tasks from the queue.
 * You can set it up as a background process or a scheduled task in Windows.
 *
 * @package    Email Service API
 * @subpackage API
 * @author     Ruta Suvagiya <ruta.suvagiya@tcs.com>
 * @version    1.0.0
 */

set_time_limit(0);

require __DIR__ . '/../vendor/autoload.php';
require 'files/providerList.php';

use Predis\Client;
use App\ProviderManager;
use App\Strategy\RoundRobinStrategy;
use App\Template\EmailTemplateManager;

// Redis client
$redis = new Client();

echo "Worker started. Waiting for email tasks...\n";

// Inject providers into RoundRobinStrategy
$strategy = new RoundRobinStrategy($providers);
$templateManager = new EmailTemplateManager();
// Inject strategy and templateManager object into ProviderManager
$emailService = new ProviderManager($strategy, $templateManager);

//Make infinite loop to
while (true) {
    // Fetch an email task from the queue
    $emailData = $redis->lpop('email_queue');

    //If Queue has data then proceed with email sending
    if ($emailData) {
        $emailData = json_decode($emailData, true);

        try {
            // Validate Request
            if (!isset($emailData['template_name'], $emailData['to'], $emailData['data'])) {
                http_response_code(400);
                echo json_encode(["error" => "Invalid inputs"]);
                exit;
            }

            // Send an email
            $emailService->sendEmail($emailData['to'], $emailData['data'], $emailData['template_name']);
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$e->getMessage()}\n";
        }
    } else {
        // Sleep for a while before checking the queue again
        sleep(1);
    }
}
