<?php

/**
 * This file handles API request calls
 *
 * @package    Email Service API
 * @subpackage API
 * @author     Ruta Suvagiya <ruta.suvagiya@tcs.com>
 * @version    1.0.0
 */
namespace App\API;

use App\API\JsonProcessorFactory;
use Exception;

/**
 * Class RequestHandler
 *
 * Handles incoming API requests, processes them, and returns appropriate responses.
 * This class supports POST methods and json format inputs.
 */
class RequestHandler
{
    /**
     * Processes an incoming HTTP request.
     *
     * This method verifies input type of API and base on that
     * It fetch the API data/file and call JsonProcessorFactory method to process content.
     * and returns the corresponding HTTP response.
     * 
     * @return The HTTP response after processing the request.
     *
     * @throws \Exception If the request cannot be processed due to invalid Json data or Unsupported input type.
     */
    public function handleRequest(): void
    {
        try {

            //Check if input type is json then fetch json data and process with validation and queue add
            if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {

                $jsonData = file_get_contents('php://input');
                $processor = JsonProcessorFactory::create('data'); //JsonDataProcessor object creation
                $processor->process($jsonData);

            } elseif (!empty($_FILES['json_file'])) { //If API has attached file then fetch data and add in process

                $jsonFilePath = $_FILES['json_file']['tmp_name'];
                $processor = JsonProcessorFactory::create('file');  //JsonFileProcessor object creation
                $processor->process($jsonFilePath);

            } else {
                http_response_code(400);
                echo json_encode(["error" => 'Unsupported content type.']);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}
