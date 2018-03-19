<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 19-03-18
 * Time: 11:02
 */

namespace edwrodrig\static_generator;


use phpDocumentor\Reflection\DocBlockFactory;

class PageMetadata
{
    private $reader = null;

    public function __construct(string $filename) {

        $this->reader = $this->get_first_comment_file($filename);
    }

    private function get_first_comment_file(string $filename) {
        $tokens = token_get_all(file_get_contents($filename));
        foreach ($tokens as $token) {
            if ($token[0] !== T_COMMENT) continue;
            $content = $token[1];
            $factory = DocBlockFactory::createInstance();
            return $factory->create($content);
        }
        return null;
    }


    /**
     * @return string
     * @throws exception\TemplateClassDoesNotExistsException
     */
    public function get_template_class() : ?string {

        if ( is_null($this->reader) )
            return null;

        if ( !$this->reader->hasTag('template') )
            return null;

        $template_class = $this->reader->getTagsByName('template')[0];
        if ( !class_exists($template_class) )
            throw new exception\TemplateClassDoesNotExistsException($template_class);
        return $template_class;
    }

    public function get_type() : string {
        if ( is_null($this->reader))
            return "interpret";

        return $this->reader->getTagsByName('type')[0] ?? 'interpret';
    }

    /**
     * @return array
     * @throws exception\WrongDataException
     */
    public function get_data() {
        if ( is_null($this->reader) )
            return [];

        $data = $this->reader->getTagsByName('data')[0] ?? '{}';
        $parsed_data = json_decode($data, true);

        if ( is_array($parsed_data) ) {
            return $parsed_data;
        } else {
            throw new exception\WrongDataException($parsed_data);
        }

    }
}