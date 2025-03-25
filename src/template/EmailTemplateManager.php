<?php

/**
 * Email Template Management Library
 *
 * This file contains the definition of the EmailTemplateManager class,
 * responsible for retrieving and rendering email templates.
 *
 * @package    Email Service API
 * @subpackage API
 * @author     Ruta Suvagiya <ruta.suvagiya@tcs.com>
 * @version    1.0.0
 */

namespace App\Template;

use Exception;

/**
 * Class EmailTemplateManager
 *
 * Manages the retrieval and rendering of email templates.
 * Adheres to the Single Responsibility Principle by focusing solely on template management.
 *
 */
class EmailTemplateManager
{
    private $templates;

    /**
     * Constructor
     *
     * @param string $templateFile Path containing the email template file.
     */
    public function __construct(string $templateFile = __DIR__ . "/email_templates.json")
    {

        if (!file_exists($templateFile)) {
            throw new Exception("Template file not found!");
        }
        $this->templates = json_decode(file_get_contents($templateFile), true);
    }

    /**
     * Retrieves the name of template if exists else null.
     *
     * @param string $name Name of the template.
     *
     * @return array The content of the template.
     */
    public function getTemplate(string $name): ?array
    {
        return $this->templates[$name] ?? null;
    }

    /**
     * Renders the email template by replacing placeholders with actual data.
     *
     * @param string $name Template name.
     * @param array  $data Associative array of data to replace in the template.
     *
     * @return array subject of array and The rendered template with placeholders replaced by actual data.
     */
    public function renderTemplate(string $name, array $data): array
    {
        $template = $this->getTemplate($name);
        if (!$template) {
            throw new Exception("Template not found.");
        }

        // Replace placeholders in subject & body
        $subject = $this->replacePlaceholders($template['subject'], $data);
        $body = $this->replacePlaceholders($template['body'], $data);
        return ['subject' => $subject, 'body' => $body];
    }

    /**
     * Replace placeholders with actual data and return content to send in email
     *
     * @param string $content Enail template content with placeholders
     * @param array  $data    Associative array of data to replace in the template.
     *
     * @return string placeholder replaced string
     */
    private function replacePlaceholders(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            $content = str_replace("{{" . $key . "}}", htmlspecialchars($value, ENT_QUOTES, 'UTF-8'), $content);
        }
        return $content;
    }
}
