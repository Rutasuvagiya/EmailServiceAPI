<?php

/**
 * This file validates email data passed as json format in API and push to Reddis queue
 *
 * @package    Email Service API
 * @subpackage API
 * @author     Ruta Suvagiya <ruta.suvagiya@tcs.com>
 * @version    1.0.0
 */

namespace App\API;

use App\API\JsonProcessor;
use Exception;

/**
 * Class JsonDataProcessor
 *
 * Provides methods to process and manipulate JSON data and adding in Redis queue.
 */
class JsonDataProcessor extends JsonProcessor
{
    /**
     * Process method to validate inputs and push json data in email queue
     *
     * @param string $jsonData input json data to push in queue
     *
     * @throws Exception If validation or data push in redis fails.
     */
    public function process(string $jsonData)
    {
        try {
            if ($this->validateData($jsonData)) {

                //Push json data to email queue in Redis
                $this->redis->rpush('email_queue', $jsonData);

                //Returns success response with code 200 to API call
                http_response_code(200);
                echo json_encode(["status" => "success", "message" => "Email(s) queued successfully."]);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
