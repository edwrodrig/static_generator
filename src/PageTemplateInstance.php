<?php
namespace edwrodrig\static_generator;

class PageTemplateInstance extends Page {

public function prepare_output() {
  if ( is_null($this->output_relative_path) ) {
    $output = preg_replace('/\.php$/', '', $this->input_relative_path);
    $this->output_relative_path = $output;
  }
  return parent::prepare_output();
}

public function generate() {
  $output = $this->prepare_output();

  $this->log(sprintf("Rendering file [%s]...", $this->output_relative_path));
  file_put_contents($output, $this->generate_string());
  $this->log("DONE\n");
}

public function generate_string() {
  $php_file = $this->input_absolute_path;
  self::push($this);

  $metadata = \edwrodrig\static_generator\Utils::get_comment_data($php_file, 'METADATA');
  $metadata = json_decode(trim($metadata), true);
  if ( !isset($metadata['template']) )
    throw new \Exception('TEMPLATE_NOT_DEFINED');

  $template_class = $metadata['template'];

  if ( !class_exists($template_class) )
    throw new \Exception('TEMPLATE_CLASS_DOES_NOT_EXISTS');

  $template = new $template_class;
  $template->metadata = $metadata;
  $template->template_content['body'] = function() use($php_file) {
    include($php_file);
  };

  $content = strval($template);
  self::pop();

  return $content; 
}

}
