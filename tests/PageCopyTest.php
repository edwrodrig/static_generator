<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 12-05-18
 * Time: 22:29
 */

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\Context;
use edwrodrig\static_generator\exception\CopyException;
use edwrodrig\static_generator\PageCopy;
use edwrodrig\static_generator\util\TemporaryLogger;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class PageCopyTest extends TestCase
{

    private vfsStreamDirectory $root;

    public function setUp() : void {
        $this->root = vfsStream::setup();
    }

    /**
     * @throws CopyException
     */
    public function testGenerate()
    {
        $logger = new TemporaryLogger;

        $context = new Context(__DIR__, $this->root->url());
        $context->setLogger($logger);

        $page = new PageCopy(
            'files/test_dir/hola.php',
            $context
        );
        $page->generate();

        $expected_log = <<<LOG
Copying file [files/test_dir/hola.php]...DONE
LOG;


        $this->assertEquals('files/test_dir/hola.php', $page->getTargetRelativePath());
        $this->assertEquals($this->root->url() . '/files/test_dir/hola.php', $page->getTargetAbsolutePath());
        $this->assertFileExists($page->getTargetAbsolutePath());
        $this->assertEquals($expected_log, $logger->getTargetData());
        $this->assertFileEquals($page->getTargetAbsolutePath() , $page->getSourceAbsolutePath());

    }
}
