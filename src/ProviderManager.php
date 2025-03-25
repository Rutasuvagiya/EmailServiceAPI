<?php

/**
 * Provider manager/ Email service manager
 *
 * Adapter design pattern to integrate different email service providers.
 * This file contains the definition of the ProviderManger class,
 * responsible for render template, select provider and send email
 *
 * @package    Email Service API
 * @subpackage API
 * @author     Ruta Suvagiya <ruta.suvagiya@tcs.com>
 * @version    1.0.0
 */

namespace App;

use Exception;
use App\Template\EmailTemplateManager;
use App\Strategy\Interfaces\ProviderSelectionStrategy;

/**
 * ProviderManager Class
 *
 * This class provides functionality to send emails using selected providers and templates.
 * It integrates with different email service providers and utilizes templates for email content.
 *
 */
class ProviderManager
{
    // private $providers = [];
    private $strategy;
    private $templateManager;

    // Specify the log file path
    public $logFile = __DIR__ . '/config/emailLog.txt';

    public function __construct(ProviderSelectionStrategy $strategy, EmailTemplateManager $emailTemplateManager)
    {
        $this->strategy = $strategy;
        $this->templateManager = $emailTemplateManager;
    }

    /**
     * Sends an email using the selected provider and template.
     *
     * @param string $toEmail      The recipient's email address.
     * @param array  $templateData The data to populate the template placeholders.
     * @param string $templateName The name of the email template to use.
     *
     * @return bool true if mail sent successfully, else false
     *
     * @throws \Exception If an error occurs while sending the email.
     */
    public function sendEmail(string $to, array $data, string $templateName): bool
    {
        $failCount = 0;
        $maxRetries = 3;

        try {
            //Retrieves the content of a specified email template.
            $template = $this->templateManager->renderTemplate($templateName, $data);
        }
        catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }

            /* Loop untill max limit reach per email
            * If Any servicer provider fails, try to call next provider
            * If max limit reach then add email in failure
            */
            while ($failCount < $maxRetries) {
                $provider = $this->strategy->selectProvider();
                try {
                    //Call the service provider method which is generated dynamically from providerList.php file
                    if ($provider[1]($to, $template['subject'], $template['body'])) {
                        echo "Email sent successfully via {$provider[0]}\n";
                        file_put_contents($this->logFile, date('Y-m-d H:i:s') . " : {$template['subject']} mail sent via {$provider[0]}" . PHP_EOL, FILE_APPEND); //Add email log in log file
                        return true;
                    } else {
                        //If service provider method call fails, retry with another provider
                        echo "{$provider[0]} failed. Retrying...\n";
                    }
                } catch (Exception $e) {
                    echo "{$provider[0]} threw an exception: " . $e->getMessage() . "\n";
                }

                $failCount++;
            }
            //Add failure message in log file
            file_put_contents($this->logFile, date('Y-m-d H:i:s') . " : {$template['subject']} mail sent failed with max retries" . PHP_EOL, FILE_APPEND);
            echo "All email providers failed after $maxRetries retries.\n";
            return false;
    }
}
