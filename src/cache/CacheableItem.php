<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-03-18
 * Time: 10:23
 */

namespace edwrodrig\static_generator\cache;

use DateTime;
use JsonSerializable;

interface CacheableItem
{
    public function get_cache_key() : string;

    public function get_last_modification_time() : DateTime;

    public function get_cached_file() : string;

    public function get_output_filename() : string;

    public function cache_generate(CacheManager $cache);
}