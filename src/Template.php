<?php

namespace edwrodrig\static_generator;

abstract class Template
{
    public function bottom_up_call($method, $args = [])
    {
        $c = new \ReflectionClass($this);

        $found = false;
        $content = $args[0] ?? '';

        do {
            if ($c->hasMethod($method)) {
                $m = $c->getMethod($method);
                $c = $m->getDeclaringClass();
                $content = Util::ob_safe(function () use ($m, $content) {
                    $m->invoke($this, $content);
                });
                $found = true;
            }
        } while ($c = $c->getParentClass());

        if (!$found) {
            throw new \Exception(sprintf('Method [%s] not defined', $method));
        }
        echo $content;
    }

    public function __toString()
    {
        try {
            return Util::ob_safe(function () {
                $this->print();
            });
        } catch (\Exception $e) {
            return '';
        }
    }

    public static function create(...$args)
    {
        $class = get_called_class();
        return new $class(...$args);
    }

    public function print()
    {
    }

    public function try_print()
    {
        echo strval($this);
    }

}


