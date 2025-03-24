<?php

/**
 * This file creates instance of different classes based on API input types
 *
 * @package    Email Service API
 * @subpackage API
 * @author     Ruta Suvagiya <ruta.suvagiya@tcs.com>
 * @version    1.0.0
 */

namespace App\API;

use App\API\JsonDataProcessor;
use App\API\JsonFileProcessor;

/**
 * JsonProcessorFactory Class
 *
 * A factory class responsible for creating instances of JSON processors,
 * such as JsonDataProcessor and JsonFileProcessor. This class follows
 * the Factory Design Pattern to encapsulate the instantiation logic of
 * JSON processing objects.
 */
class JsonProcessorFactory
{
    /**
     * Creates and returns an instance of a JSON processor.
     *
     * Depending on the provided type, this method instantiates either
     * a JsonDataProcessor or JsonFileProcessor.
     *
     * @param string $type The type of JSON processor to create ('data' or 'file').
     * @return mixed An instance of JsonDataProcessor or JsonFileProcessor.
     * @throws \Exception If an input type is not valid.
     */
    public static function create(string $inputType)
    {
        if ($inputType === 'data') {
            return new JsonDataProcessor();
        } elseif ($inputType === 'file') {
            return new JsonFileProcessor();
        }

        throw new Exception('Invalid input type.');
    }
}
