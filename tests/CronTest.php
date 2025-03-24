<?php

/**
 * This class tests email send functionality with dynamic provider selection and
 * dynamic template selection without mock
 */

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\ProviderManager;
use App\Strategy\RoundRobinStrategy;
use App\Template\EmailTemplateManager;

class CronTest extends TestCase
{
    private array $providers;
    private $roundRobinStrategy;
    private ProviderManager $providerManager;
    private EmailTemplateManager $templateManager;

    protected function setUp(): void
    {
        $this->providers = [
            "sendgrid" => function ($to, $subject, $body) {return true;},
            "mailgun" => function ($to, $subject, $body) {return false;},
            "smtp" => function ($to, $subject, $body) {return true;},
            "sample1" => function ($to, $subject, $body) {return false;},
            "sample2" => function ($to, $subject, $body) {return true;},
            "sample3" => function ($to, $subject, $body) {return false;}
        ];

        $this->roundRobinStrategy = new RoundRobinStrategy($this->providers);
        $this->templateManager = new EmailTemplateManager();

        $this->providerManager = new ProviderManager($this->roundRobinStrategy, $this->templateManager);
    }


    /**
     * @dataProvider EmailValidData
     */
    public function testSuccessfulEmailSending($to, $data, $templateName)
    {
        $this->assertTrue($this->providerManager->sendEmail($to, $data, $templateName));
    }

    public function EmailValidData()
    {
        return [
            [

                'to' => 'tracikinney@kozgene.com',
                'data' => ['name' => 'Corine'],
                'template_name' => 'welcome_email'
            ],
            [
                'to' => 'test@nca.com',
                'data' => ['name' => 'Elsa', "reset_link" => "<a>Reset Password</a>"],
                'template_name' => 'password_reset'
            ]
        ];
    }

    public function testFailoverMechanism()
    {

        $providers = [
            "sendgrid" => function ($to, $subject, $body) {return false;},
            "mailgun" => function ($to, $subject, $body) {return false;},
            "smtp" => function ($to, $subject, $body) {return true;},
            "sample1" => function ($to, $subject, $body) {return false;},
            "sample2" => function ($to, $subject, $body) {return false;},
            "sample3" => function ($to, $subject, $body) {return true;}
            
        ];

        $roundRobinStrategy = new RoundRobinStrategy($providers);

        $emailService = new ProviderManager($roundRobinStrategy, $this->templateManager);

        $this->assertTrue($emailService->sendEmail("user@example.com", ['name' => 'Corine'], "welcome_email"));
    }

    public function testMaximumFailoverAttepts()
    {

        $providers = [
            "sendgrid" => function ($to, $subject, $body) {return false;},
            "mailgun" => function ($to, $subject, $body) {return false;},
            "smtp" => function ($to, $subject, $body) {return false;},
            "sample1" => function ($to, $subject, $body) {return false;},
            "sample2" => function ($to, $subject, $body) {return false;},
            "sample3" => function ($to, $subject, $body) {return true;}
        ];

        $roundRobinStrategy = new RoundRobinStrategy($providers);

        $emailService = new ProviderManager($roundRobinStrategy, $this->templateManager);

        $this->assertFalse($emailService->sendEmail("user@example.com", ['name' => 'Corine'], "welcome_email"));
    }

    public function testAllProvidersFail()
    {
        $providers = [
            "sendgrid" => function ($to, $subject, $body) {return false;},
            "mailgun" => function ($to, $subject, $body) {return false;},
            "smtp" => function ($to, $subject, $body) {return false;},
            "sample1" => function ($to, $subject, $body) {return false;}
        ];

        $roundRobinStrategy = new RoundRobinStrategy($providers);

        $emailService = new ProviderManager($roundRobinStrategy, $this->templateManager);
        $this->assertFalse($emailService->sendEmail("user@example.com", ['name' => 'Corine'], "welcome_email"));
    }
}
