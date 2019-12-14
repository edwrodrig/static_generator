<?php
declare(strict_types=1);

namespace test\edwrodrig\static_generator\cache;

use DateTime;
use edwrodrig\static_generator\cache\CacheableItem as BaseCacheableItem;
use edwrodrig\static_generator\cache\CacheManager;

class CacheableItem implements BaseCacheableItem
{
    private string $key;

    private DateTime $date;

    private string $salt;

    public function __construct(string $key, DateTime $date, string $salt) {
        $this->key = $key;
        $this->date = $date;
        $this->salt = $salt;
    }

    public function getKey() : string {
        return $this->key;
    }

    public function getLastModificationTime() : DateTime {
        return $this->date;
    }

    public function generate(CacheManager $manager) {

        touch($manager->prepareCacheFile($this));
    }

    public function getTargetRelativePath() : string {
        return $this->key .'_' . $this->salt;
    }

    public function getAdditionalData(): array
    {
        return [
            'hola' => 1,
            'chao' => 2
        ];
    }
}

