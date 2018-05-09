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

/**
 * Class CacheEntry
 * This represent a entry in the {@see CacheIndex cache index}.
 * @package edwrodrig\static_generator\cache
 */
class CacheEntry implements JsonSerializable
{
    /**
     * The date when this entry was generated. This value is always generated internally.
     *
     * Internally this date should be always greater than the {@see CacheableItem::getLastModificationDate()}
     * @var DateTime
     */
    protected $generation_date;

    /**
     * The relative path filename that where cached.
     * @var string
     */
    protected $relative_path;

    /**
     * The very identifier of this cache entry.
     *
     * It must be unique between all entries in the {@see CacheIndex index}
     * @var string
     */
    protected $key;


    public function getKey() : string {
        return $this->key;
    }


    public static function createFromItem(CacheableItem $item, CacheManager $manager) {
        $item->generate($manager);

        $entry = new self;
        $entry->relative_path = $item->getTargetRelativePath();
        $entry->output_filename = $item->get_output_filename();
        $entry->generation_date = new DateTime();
        $entry->key = $item->getKey();
        return $entry;
    }

    public static function create_from_array(array $data) {
        $entry = new self;
        $entry->relative_path = $data['cached_file'];
        $entry->generation_date = new DateTime();
        $entry->generation_date->setTimestamp((int)$data['generation_date']);
        $entry->output_filename = $data['output_filename'];
        $entry->key = $data['cache_key'];
        return $entry;
    }

    public function getGenerationDate() : DateTime {
        return $this->generation_date;
    }

    public function getRelativePath() : string {
        return $this->relative_path;
    }

    public function jsonSerialize() {
        return [
            'cache_key' => $this->key,
            'generation_date' => $this->generation_date->getTimestamp(),
            'output_filename' => $this->output_filename,
            'cached_file' => $this->relative_path
        ];
    }
}