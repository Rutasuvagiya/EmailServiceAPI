<?php

/**
 * Round Robin Strategy
 *
 * Strategy pattern to fetch providers from the list in cyclic order.
 *
 * @package    Email Service API
 * @subpackage API
 * @author     Ruta Suvagiya <ruta.suvagiya@tcs.com>
 * @version    1.0.0
 */

namespace App\Strategy;

use App\Strategy\ProviderSelectionStrategy;
use Exception;

/**
 * RoundRobinStrategy class
 *
 * returns provider name and function to send email.
 * It stores index internally and fetch provider base on index which increments in cyclic order
 */
class RoundRobinStrategy implements ProviderSelectionStrategy
{
    private $index = 0;
    private array $providers;
    private array $keys;

    /**
     * constructor
     *
     * @param array $providers array of providers with functions (retrived from providerList.php)
     */
    public function __construct(array $providers)
    {
        if (empty($providers)) {
            throw new Exception("Array cannot be empty.");
        }
        $this->providers = $providers;
        $this->keys = array_keys($providers); // Store keys for iteration
    }


    /**
     * Selects the next service provider in the round-robin rotation.
     *
     * This method returns the next provider and updates the internal index to
     * ensure cyclic distribution.
     *
     * @return array Provider name and function.
     */
    public function selectProvider(): array
    {

        $key = $this->keys[$this->index]; // Get current key
        $provider = $this->providers[$key]; // Get value
        $this->index = ($this->index + 1) % count($this->providers); // Move to next

        return [$key,$provider]; // Return key-value pair
    }
}
