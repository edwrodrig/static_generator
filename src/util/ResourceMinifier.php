<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\util;

use edwrodrig\static_generator\util\Util;
use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;

class ResourceMinifier
{

    public $sources = [];

    /**
     * @return \Generator
     * @throws \edwrodrig\static_generator\util\exception\FileDoesNotExistsException
     */
    public function iterate_sources()
    {
        foreach (Util::iterate_files($this->sources) as $source) {
            $filename = $source->getPathname();
            $ext = $source->getExtension();
            if (in_array($ext, ['css', 'js'])) {
                yield [
                    'absolute_path' => $filename,
                    'type' => $ext
                ];
            }
        }
    }

    /**
     * @return JS
     * @throws \edwrodrig\static_generator\util\exception\FileDoesNotExistsException
     */
    public function js() : JS
    {
        $minifier = new JS;

        foreach ($this->iterate_sources() as $source) {
            if ($source['type'] !== 'js') continue;
            $minifier->add($source['absolute_path']);
        }

        return $minifier;
    }

    /**
     * @return CSS
     * @throws \edwrodrig\static_generator\util\exception\FileDoesNotExistsException
     */
    public function css() : CSS
    {
        $minifier = new CSS;

        foreach ($this->iterate_sources() as $source) {
            if ($source['type'] !== 'css') continue;
            $minifier->add($source['absolute_path']);
        }

        return $minifier;

    }

}


