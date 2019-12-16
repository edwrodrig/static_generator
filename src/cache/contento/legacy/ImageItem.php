<?php
declare(strict_types=1);


namespace edwrodrig\static_generator\cache\contento\legacy;

use DateTime;
use edwrodrig\contento\collection\legacy\Collection;
use edwrodrig\file_cache\ImageItem as BaseImageItem;
use Exception;

/**
 * Class ImageItem
 * @package edwrodrig\static_generator\cache\contento\legacy
 * @deprecated
 */
class ImageItem extends BaseImageItem
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
     * @var null
     */
    private $source_filename = null;


    private string $id;

    /**
     * ImageItem constructor.
     * @param Collection $server
     * @param array $data
     * @param int $width
     * @throws Exception
     */
    public function __construct(Collection $server, array $data, int $width = 1000) {

        parent::__construct('', '', $width);
        $this->server = $server;
        $this->last_modification_date = new DateTime($data['time']);
        $this->id = $data['id'];
        $this->filename = $this->id;

    }

    /**
     * @param Collection $server
     * @param array $data
     * @return ImageItem
     * @throws Exception
     */
    public static function createFromArray(Collection $server, array $data) {
        return new self($server, $data);
    }

    public function getId() : string {
        return $this->id;
    }

    public function getSourceFilename() : string {
        if ( is_null($this->source_filename) ) {
            $this->source_filename = tempnam(sys_get_temp_dir(),'li_');
            file_put_contents($this->source_filename, $this->server->getImage($this->id));

            $type = mime_content_type($this->source_filename);
            if ( $type == 'image/jpeg')
                $this->setTargetExtension('jpg');
            else if ( $type = 'image/png' )
                $this->setTargetExtension('png');
        }
        return $this->source_filename;
    }

    public function getLastModificationTime() : DateTime {
        return $this->last_modification_date;
    }

}