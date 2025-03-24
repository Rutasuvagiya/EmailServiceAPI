<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\MockPhpInputStream;
use App\API\RequestHandler;

class RequestHandlerTest extends TestCase
{
    private $client;
    private RequestHandler $requestHandler;

    protected function setUp(): void
    {
        stream_wrapper_unregister('php');
        stream_wrapper_register('php', 'Tests\MockPhpInputStream');

        $this->requestHandler = new RequestHandler();
    }

    protected function tearDown(): void
    {
        stream_wrapper_restore('php');
    }

    public function testHandlerWithValidContentType()
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $GLOBALS['mocked_input'] = json_encode([
            'template_name' => 'welcome_email',
            'to' => 'test@nca.com',
            'data' => ['name' => 'Maisa']
        ]);

        // Start output buffering
        ob_start();

        // Include the script that handles the request
        $this->requestHandler->handleRequest();

        // Get the output
        $response = ob_get_clean();

        // Assert the response
        $this->assertJson($response);
        $data = json_decode($response, true);
        $this->assertEquals('success', $data['status']);
    }

    public function testHandlerWithInvalidContentType()
    {
        $_SERVER['CONTENT_TYPE'] = 'application/xml';
        $GLOBALS['mocked_input'] = json_encode([
            'template_names' => 'welcome_email',
            'to' => 'test@nca.com',
            'data' => ['name' => 'Maisa']
        ]);

        // Start output buffering
        ob_start();

        // Include the script that handles the request
        $this->requestHandler->handleRequest();

        // Get the output
        $response = ob_get_clean();

        // Assert the response
        $this->assertJson($response);
        $data = json_decode($response, true);
        $this->assertEquals('Unsupported content type.', $data['error']);
    }

    public function testHandlerWithValidFile()
    {
        // Simulate the $_FILES superglobal
        $_FILES = [
            'json_file' => [
                'name' => 'valid.txt',
                'type' => 'text/plain',
                'tmp_name' => __DIR__ . '/valid.txt',
                'error' => UPLOAD_ERR_OK,
                'size' => 123,
            ],
        ];

        // Start output buffering
        ob_start();

        // Include the script that handles the request
        $this->requestHandler->handleRequest();

        // Get the output
        $response = ob_get_clean();

        // Assert the response
        $this->assertJson($response);
        echo $response;
        $data = json_decode($response, true);
        $this->assertEquals('success', $data['status']);
    }

    public function testHandlerWithInValidFile()
    {
        // Simulate the $_FILES superglobal
        $_FILES = [
            'json_file' => [
                'name' => 'invalid.txt',
                'type' => 'text/plain',
                'tmp_name' => __DIR__ . '/invalid.txt',
                'error' => UPLOAD_ERR_OK,
                'size' => 123,
            ],
        ];

        // Start output buffering
        ob_start();

        // Include the script that handles the request
        $this->requestHandler->handleRequest();

        // Get the output
        $response = ob_get_clean();

        // Assert the response
        $this->assertJson($response);
        echo $response;
        $data = json_decode($response, true);
        $this->assertEquals('Invalid JSON data.', $data['error']);
    }

    public function testHandlerWithInvalidJson()
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $GLOBALS['mocked_input'] = '{
            "to": "tracikinney@kozgene.com",
            "data": {
            "name": "Corinaa",
            "reset_link": "<a>Reset Password</a>",
            "company": "JOVIOLD"
            },
            "template_name": "subscription_email"
            }}';

        // Start output buffering
        ob_start();

        // Include the script that handles the request
        $this->requestHandler->handleRequest();

        // Get the output
        $response = ob_get_clean();

        // Assert the response
        $this->assertJson($response);
        $data = json_decode($response, true);
        $this->assertEquals('Invalid JSON data.', $data['error']);
    }
}
