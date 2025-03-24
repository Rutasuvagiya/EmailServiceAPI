<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Template\EmailTemplateManager;

class TemplateTest extends TestCase
{
    public function testInvalidTemplateFile()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Template file not found!');
        $template = new EmailTemplateManager('test');
    }

    public function testInvalidTemplateName()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Template not found.');
        $template = new EmailTemplateManager();
        $template->renderTemplate('test', []);
    }
}
