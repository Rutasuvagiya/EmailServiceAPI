<?php

/**
 * This file validates email data passed as json format in attached file of API and push to Reddis queue
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
 * Class JsonFileProcessor
 *
 * Provides methods to process and manipulate JSON data from attached file and adding in Redis queue.
 */
class JsonFileProcessor extends JsonProcessor
{
    /**
     * Process method to validate input file content and push json data in email queue
     *
     * @param string $filePath input temp file path
     *
     * @throws Exception If validation or data push in redis fails.
     */
    public function process(string $filePath)
    {
        try {

            // Get the MIME type using Fileinfo
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filePath);
            finfo_close($finfo);

            if ($mimeType === 'application/json'  || $mimeType === 'text/plain') {
                //Fech file content
                $jsonData = file_get_contents($filePath);

                //Check if content of file has valid json data or not
                if ($this->validateData($jsonData)) {
                    $queueData = json_decode($jsonData, true);

                    //Loop to fetch each record of file and push to Redis queue
                    foreach ($queueData as $email) {

                        // Add email task to the queue
                        $this->redis->rpush('email_queue', json_encode($email));
                    }

                    //Returns success response with code 200 to API call
                    http_response_code(200);
                    echo json_encode(["status" => "success", "message" => "Email(s) queued successfully."]);
                }
            }
            else
            {
                throw new Exception('Invalid File type.');
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
