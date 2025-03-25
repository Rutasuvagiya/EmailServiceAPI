<?php

/**
 * API Email data queue Abstract class
 *
 * This file includes JSON data validation and
 * abstract method process which reads json data/file and push to Redis queue in subclass
 *
 * @package    Email Service API
 * @subpackage API
 * @author     Ruta Suvagiya <ruta.suvagiya@tcs.com>
 * @version    1.0.0
 */

namespace App\API;

use Predis\Client;
use Exception;

/**
 * Abstract class for handling JSON data.
 *
 * This class provides a template to validate input JSON data.
 * Subclasses must implement the abstract methods to define specific
 * data handling behaviors.
 */
abstract class JsonProcessor
{
    public $redis;
    /**
     * Constructor
     *
     * Creates Redis object which can be used to push data in queue.
     */
    public function __construct()
    {
        // Redis client
        $this->redis = new Client();
    }

    /**
     * checks if input has valid json data or not
     *
     * @param string $jsonData json data to validate
     *
     * @return bool true if valid date, else false
     *
     * @throws Exception If data is not valid
     */
    public function validateData(string $jsonData): bool
    {
        // Attempt to decode the incoming RAW post data from JSON.
        $decoded = json_decode($jsonData, true);

        // If json_decode failed, the JSON is invalid.
        if (!is_array($decoded)) {
            throw new Exception('Invalid JSON data.', 400);
            return false;
        } else {
            return true;
        }
    }

    /**
     * Process Json data - decode, validate and push to Redis queue
     *
     * @param string $jsonData json input data/json file name
     */
    abstract public function process(string $jsonData);
}
