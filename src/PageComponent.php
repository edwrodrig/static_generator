<?php

namespace edwrodrig\static_generator;

abstract class PageComponent
{

    public $prefix_pattern = '@@@';
    public $prefix;

    public function __construct($prefix = null)
    {
        if (empty($prefix)) {
            $prefix = uniqid();
        }
        $this->prefix = $prefix;
    }

    public function print()
    {
        ob_start();
        $this->content();
        $content = ob_get_clean();

        $content = str_replace($this->prefix_pattern, $this->prefix, $content);
        echo $content;
    }

    abstract public function content();

    static public function include(string $filename, ?string $prefix = null)
    {
        $obj = new class($prefix, $filename) extends PageComponent
        {
            private $filename;

            function __construct(string $prefix, string $filename)
            {
                parent::__construct($prefix);
                $this->filename = $filename;
            }

            function content()
            {
                require($this->filename);
            }
        };

        $obj->print();
    }

}

