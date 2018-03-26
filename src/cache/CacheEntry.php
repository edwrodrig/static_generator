<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 24-03-18
 * Time: 23:08
 */

namespace edwrodrig\static_generator\cache;


trait CacheEntry
{

    protected $last_modification_time;

    protected $generate_

    abstract public function get_cache_key() : string;

    abstract public function get_last_modification_time() : DateTime;

    public function cache_generate() {
            
    }

    abstract public function get_url();

    public function get_cache_time() : DateTime;

    abstract public function remove_cached();

    public function is_expired(DateTime $time) {
        return $this->get_cache_type() < $time;
    }


}