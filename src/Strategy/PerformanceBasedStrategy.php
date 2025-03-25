<?php

/**
 * PerformanceBasedStrategy Class
 *
 * Implements a strategy for selecting service providers based on their performance metrics.
 *
 */

namespace App\Strategy;

use App\Strategy\Interfaces\ProviderSelectionStrategy;

class PerformanceBasedStrategy implements ProviderSelectionStrategy
{
    private array $successRates = [];
    private array $providers;

    /**
     * Constructor
     *
     * Initializes the strategy with a list of service providers and their success rates.
     *
     * @param array $providers Array of service provider instances, each containing performance data.
     */
    public function __construct(array $providers)
    {
    }

    /**
     * Selects the best service provider based on performance metrics.
     *
     * This method evaluates the performance of each provider and selects the one
     * with the optimal metrics for handling the current task.
     *
     * @return array The selected service provider instance.
     *
     * @throws \Exception If no service providers are available.
     */
    public function selectProvider(): array
    {
        /*
        $key = array_key_first($this->providers);
        return [$key,$this->providers[$key]]; // Default fallback
        */
        return [];
    }
}
