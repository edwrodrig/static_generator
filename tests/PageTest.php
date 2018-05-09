<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 08-05-18
 * Time: 21:47
 */

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\Page;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{

    public function testUrl()
    {
        $context = new Context('source', 'target');
        $context->setTargetWebPath('es');
        $page = new Page('out.html', $context);
        $this->assertEquals('out.html', $page->getTargetRelativePath());
        $this->assertEquals('target/out.html', $page->getTargetAbsolutePath());
        $this->assertEquals('in.html', $page->url('in.html'));
        $this->assertEquals('/es/in.html', $page->url('/in.html'));
    }

    public function testCurrentUrl()
    {
        $context = new Context('', '');
        $context->setTargetWebPath('es');
        $page = new Page('out.html', $context);
        $this->assertEquals('/es/out.html', $page->currentUrl());
    }
}
