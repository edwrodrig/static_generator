<?php

namespace edwrodrig\static_generator\cache;

use edwrodrig\image\Image;
use edwrodrig\image\Size;

class ImageItem extends FileItem
{
    protected $width;
    protected $height;
    protected $resize_mode = 'copy';

    public function __construct(string $root_path, string $file, string $version = '', int $width = 1000)
    {
        parent::__construct($root_path, $file, $version);
        $this->width = $width;

        if (pathinfo($file, PATHINFO_EXTENSION) == 'svg')
            $this->target_extension = 'png';

    }

    /**
     * Command to resize contain the image
     *
     * Uses the behavior of (@see Image::contain()}.
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function resizeContain(int $width, int $height) : ImageItem {
        $this->width = $width;
        $this->height = $height;
        $this->version = $width . 'x' . $height . '_contain';
        $this->resize_mode = 'contain';

        return $this;
    }

    /**
     * Command to resize cover the image
     *
     * Uses the behavior of (@see Image::cover()}.
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function resizeCover(int $width, int $height) : ImageItem {
        $this->width = $width;
        $this->height = $height;
        $this->version = $width . 'x' . $height . '_cover';
        $this->resize_mode = 'cover';

        return $this;
    }


    /**
     * Process image.
     *
     * This method is used by {@see ImageItem::generate()} and will be overriden
     * if you want to add more functionality to this object.
     * @param Image $image
     */
    protected function process(Image $image) {
    }

    /**
     * @param CacheManager $manager
     * @throws \ImagickException
     * @throws \edwrodrig\image\exception\ConvertingSvgException
     * @throws \edwrodrig\image\exception\InvalidImageException
     * @throws \edwrodrig\image\exception\InvalidSizeException
     * @throws \edwrodrig\image\exception\WrongFormatException
     * @uses ImageItem::process()
     */
    public function generate(CacheManager $manager) {

        $img = Image::createFromFile($this->getSourceFilename(), $this->width);

        $this->process($img);

        if ( $this->resize_mode == 'contain' ) {
            $img->contain(new Size($this->width, $this->height));

        } else if ( $this->resize_mode == 'cover' ) {
            $img->cover(new Size($this->width, $this->height));
        }

        if ( $this->target_extension == 'jpg') {
            $img->optimizePhoto();
        } else {
            $img->optimize();
        }


        $img->writeImage($manager->prepareCacheFile($this));
    }

}
