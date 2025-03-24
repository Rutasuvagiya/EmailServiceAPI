<?php

/**
 * ProviderSelectionStrategy Interface
 *
 * Strategy pattern to choose providers dynamically
 *
 * @package    Email Service API
 * @subpackage API
 * @author     Ruta Suvagiya <ruta.suvagiya@tcs.com>
 * @version    1.0.0
 */

namespace App\Strategy;

/**
 * ProviderSelectionStrategy Interface
 *
 * Implementing classes should provide logic to determine which email provider
 * to use for sending emails, facilitating flexibility and scalability in the
 * email sending process.
 */
interface ProviderSelectionStrategy
{
    /**
     * Selects the appropriate email service provider.
     *
     * This method determines which email provider to use based on configurations.
     *
     * @return string The identifier or name of the selected email provider.
     */
    public function selectProvider(): array;
}
