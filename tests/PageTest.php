<?php

namespace test\edwrodrig\static_generator;

use edwrodrig\static_generator\util\FileData;

class PageTest extends \PHPUnit\Framework\TestCase
{


    function createProvider()
    {
        return [
            [
                \edwrodrig\static_generator\PagePhp::class,
                '@raw',
                'h.php'
            ],
            [
                \edwrodrig\static_generator\PagePhp::class,
                '@silent',
                'h.php'
            ],
            [
                \edwrodrig\static_generator\PagePhp::class,
                '',
                'h.php'
            ],
            [
                \edwrodrig\static_generator\PagePhp::class,
                '@template',
                'h.php'
            ],
            [
                \edwrodrig\static_generator\PageCopy::class,
                '',
                'h.jpg'
            ]


        ];

    }

    /**
     * @dataProvider createProvider
     * @param $expected
     * @param $metadata
     * @param $input_file
     */
    function testCreate($expected, $metadata, $input_file)
    {
        $filename = '/tmp/' . $input_file;
        $file = file_put_contents($filename, "<?php\n/*\n" . $metadata . "\n*/?>");


        $page = \edwrodrig\static_generator\Page::create(
            new FileData(0, $input_file, '/tmp'),
            '/tmp'
        );
        $this->assertInstanceOf($expected, $page);
    }

}

