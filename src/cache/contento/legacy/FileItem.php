<?php
declare(strict_types=1);


namespace edwrodrig\static_generator\cache\contento\legacy;

use DateTime;
use edwrodrig\contento\collection\legacy\Collection;
use edwrodrig\file_cache\FileItem as BaseFileItem;
use Exception;

/**
 * Class ImageItem
 * @package edwrodrig\static_generator\cache\contento\legacy
 * @deprecated
 */
class FileItem extends BaseFileItem
{
    /**
     * @var Collection
     */
    private Collection $server;

    /**
     * The last modification time
     * @var DateTime
     */
    private DateTime $last_modification_date;

    /**
     * Contains the temporary filename
     */
    private string $source_filename;

    private string $id;

    /**
     * FileItem constructor.
     * @param Collection $server
     * @param array $data
     * @throws Exception
     */
    public function __construct(Collection $server, array $data) {

        parent::__construct('', '', '');
        $this->server = $server;
        $this->last_modification_date = new DateTime($data['time']);
        $this->id = $data['id'];
        $this->filename = $this->id;

    }

    /**
     * @param Collection $server
     * @param array $data
     * @return FileItem
     * @throws Exception
     */
    public static function createFromArray(Collection $server, array $data) {
        return new self($server, $data);
    }

    public function getId() : string {
        return $this->id;
    }

    public function getSourceFilename() : string {
        if ( !isset($this->source_filename) ) {
            $this->source_filename = tempnam(sys_get_temp_dir(),'li_');
            file_put_contents($this->source_filename, $this->server->getFile($this->id));
        }
        return $this->source_filename;
    }

    public function getLastModificationTime() : DateTime {
        return $this->last_modification_date;
    }

}