<?php

/**
 * This class test API with all possible inputs without mocks and stubs.
 *
 */

namespace Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use App\API\JsonProcessorFactory;
use Exception;

class ApiTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost/emailService/',
            'http_errors' => false, // Prevent exceptions on non-2xx responses
        ]);
    }

    public function testSuccessWithJsonData()
    {
        //json variable converts inputs into json type
        $response = $this->client->post('sendEmail.php', [
            'json' => [
                'template_name' => 'welcome_email',
                'to' => 'test@nca.com',
                'data' => ['name' => 'Maisa']
            ],
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
    }

    public function testInvalidContentType()
    {
        //form_params pass inputs as array
        $response = $this->client->post('sendEmail.php', [
            'form_params' => [
                'template_names' => 'welcome_email',
                'to2' => 'test@nca.com',
                'data' => ['name' => 'Maisa']
            ],
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertEquals('Unsupported content type.', $data['error']);
    }

    public function testFileUpload()
    {
        // Path to the file you want to upload
        $filePath = __DIR__ . '/files/validEmailData.json';

        // Ensure the file exists
        $this->assertFileExists($filePath);

        // Send a POST request with the file
        $response = $this->client->post('sendEmail.php', [
            'multipart' => [
                [
                    'name'     => 'json_file',
                    'contents' => Psr7\Utils::tryFopen($filePath, 'r'),
                    'filename' => 'file',
                ]
            ]
        ]);
         // Assert the request was successful
         $this->assertEquals(200, $response->getStatusCode());
    }

    public function testInvalidFileUpload()
    {
        // Path to the file you want to upload
        $filePath = __DIR__ . '/files/invalid.json';

        // Ensure the file exists
        $this->assertFileExists($filePath);

        // Send a POST request with the file
        $response = $this->client->post('sendEmail.php', [
            'multipart' => [
                [
                    'name'     => 'json_file',
                    'contents' => Psr7\Utils::tryFopen($filePath, 'r'),
                    'filename' => 'file',
                ]
            ]
        ]);
        // Assert the request was successful
        $this->assertEquals(400, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertEquals('Invalid JSON data.', $data['error']);
    }

    public function testInvalidJson()
    {
         // Sending an invalid JSON payload
         $response = $this->client->request('POST', 'sendEmail.php', [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => '{"invalid": "json", "missing_end"' // Deliberately broken JSON
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Invalid JSON data.', $data['error']);
    }

    public function testJsonProcessorFactoryException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid input type.');
        JsonProcessorFactory::create('invalid');
    }

    protected function tearDown(): void
    {
        unset($this->client);
    }
}
