<?php
namespace ephp\web;

class Meta {

public $data;

function __construct($data) {
  $this->data = $data;
} 

const FAVICON_SIZES = [16, 32, 96, 160, 192];
const APPLE_TOUCH_SIZES = [57, 60, 72, 76, 114, 120, 114, 152, 180];

function tag_favicon($favicon, $size) {
  if ( !isset($favicon) ) return;
  $favicon = $favicon . $size . ".png"; //request image of size $sizex$size;
  printf('<link rel="icon" type="image/png" href="%s" sizes="%sx%s" />', $favicon, $size, $size);
}

function tag_apple_touch_icon($favicon, $size) {
  if ( !isset($favicon) ) return;
  $favicon = $favicon . $size . ".png";//request image of size $sizex$size
  printf('<link rel="apple-touch-icon" href="%s" sizes="%sx%s"/>', $favicon, $size, $size);
}

function tag_basic($key, $value) {
  if ( !isset($value) ) return;
  printf('<meta name="%s" content="%s" />', $key, $value);
}

function tag_twitter_card($key, $value) {
  if ( !isset($value) ) return;
  printf('<meta name="twitter:%s" content="%s" />', $key, $value);
}

function tag_open_graph($key, $value) {
  if ( !isset($value) ) return;
  printf('<meta name="og:%s" content="%s" />', $key, $value);
}

function tag_google_plus($key, $value) {
  if ( !isset($value) ) return;
  printf('<meta itemprop="%s" content="%s" />', $key, $value);
}

function tags_basic() {
  $t = 'tag_basic';
  $this->$t('author', $this->data['author'] ?? null);
  $this->$t('description', $this->description() ?? null);
}

function tags_open_graph() {
  $t = 'tag_open_graph';
  $this->$t('title', $this->title() ?? null);
  $this->$t('url', $this->data['url'] ?? null);
  $this->$t('description', $this->description() ?? null);
  $this->$t('type', $this->data['type'] ?? null);

  $image = $this->image();
  if ( isset($image) ) {
    $image = $image; //request  1200 × 630 version of the image

    $this->$t('image', $image);
    $this->$t('image:type', $this->data['image']['type'] ?? null);
    $this->$t('image:width', $this->data['image']['width'] ?? null);
    $this->$t('image:height', $this->data['image']['height'] ?? null);
  }
}

function tags_google_plus() {
  $t = 'tag_google_plus';
  $this->$t('name', $this->title());
  $this->$t('description', $this->description());
  $this->$t('image', $this->image());
}

function tags_twitter_card() {
  $t = 'tag_twitter_card';
  $type = $this->data['twitter']['type'] ?? '';
  if ( !is_array(['app', 'summary_large', 'summary']) ) return;
  $this->$t('card', $type);
  $this->$t('site', $this->data['twitter']['site'] ?? null);
  $this->$t('description', $this->description());
  $this->$t('title', $this->data['title'] ?? null);
  $this->$t('creator', $this->data['twitter']['creator'] ?? null);
  
  $image = $this->image();
  if ( isset($image) ) {
    if ( $type == 'summary_large' )
      $image = $image;//request 200x150 version of the image
    else if ( $type == 'summary' )
      $image = $image;//request 120x120 version of the image
    $this->$t('image', $image);
  }
}

function tag_title() {
  $arr = [];
  if ( isset($this->data['title']) ) $arr[] = $this->data['title'];
  else {
    if ( isset($this->data['page']['title']) ) $arr[] = $this->data['page']['title'];
    if ( isset($this->data['site']['title']) ) $arr[] = $this->data['site']['title'];
  }
  $title = implode (' | ', $arr);
  if ( empty($title) ) return;
  printf('<title>%s</title>', $title);
}

function title() {
  return $this->data['title'] ?? $this->data['page']['title'] ?? $this->data['site']['title'] ?? null;
}

function description() {
  return $this->data['description'] ?? $this->data['page']['description'] ?? $this->data['site']['description'] ?? null;
}

function image() {
  return $this->data['image']['url'] ??
         $this->data['image'] ??
         $this->data['page']['image']['url'] ??
         $this->data['page']['image'] ??
         $this->data['site']['image']['url'] ??
         $this->data['site']['image'] ?? null;
}

function tags_favicons() {
  if ( !isset($this->data['favicon']) ) return;
  foreach( self::FAVICON_SIZES as $s )
    $this->tag_favicon($this->data['favicon'], $s);
  foreach( self::APPLE_TOUCH_SIZES as $s )
    $this->tag_apple_touch_icon($this->data['favicon'], $s);
}

function __invoke() {
echo <<<EOL
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="x-ua-compatible" content="ie=edge">
EOL;
 
  $this->tag_title();
  $this->tags_basic();
  $this->tags_open_graph();
  $this->tags_google_plus();
  $this->tags_twitter_card();

  $this->tags_favicons();
}

function __toString() {
  ob_start();
  $this->__invoke();
  return ob_get_clean();
}


}

