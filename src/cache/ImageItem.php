<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\cache;

use edwrodrig\image\Image;
use edwrodrig\image\Size;

/**
 * Class ImageItem
 *
 * Use this function to cache images
 * This class works with a {@see CacheManager cache} in the following way.
 * ```
 * $file = new ImageItem('/images', 'image.jpg');
 * $cache_manager->update($file);
 * ```
 *
 * Maybe you want to override {@see ImageItem::process()} to creating images with other behaviours.
 * This class works with {@see Image SVG, PNG and JPG} file formats.
 * @api
 * @see FileItem::setSalt() to set a salt to the target filename
 * @package edwrodrig\static_generator\cache
 */
class ImageItem extends FileItem
{
    /**
     * The width of the image
     *
     * This is used in the context or {@see ImageItem::resizeContain() resizes}
     * @var int
     */
    protected $width;

    /**
     * The height of the image.
     *
     * This is used in the context or {@see ImageItem::resizeContain() resizes}
     * @var
     */
    protected $height;

    /**
     * Resize mode.
     *
     * What resize mode will be executed in {@see ImageItem::generate() generation}.
     * @var int
     */
    protected $resize_mode = self::RESIZE_MODE_COPY;

    /**
     * The image should not be resized.
     */
    const RESIZE_MODE_COPY = 0;

    /**
     * The image should be resized to be {@see Image::contain() contained}.
     */
    const RESIZE_MODE_CONTAIN = 1;

    /**
     * The image should be resized to be {@see Image::cover() cover}.
     */
    const RESIZE_MODE_COVER = 2;

    /**
     * ImageItem constructor.
     *
     * @api
     * @param string $root_path
     * @param string $file
     * @param int $width this size serves a a {@see Image::$svg_width hint} for svg files
     */
    public function __construct(string $root_path, string $file, int $width = 1000)
    {
        parent::__construct($root_path, $file, '');
        $this->width = $width;

        if (pathinfo($file, PATHINFO_EXTENSION) == 'svg')
            $this->target_extension = 'png';

    }

    /**
     * Get the image width.
     *
     * Is safe to call this after {@see ImageItem::generate()}
     * @return int
     */
    public function getWidth() : int {
        return $this->width;
    }

    /**
     * Get the image height.
     *
     * Is safe to call this after {@see ImageItem::generate()}
     * @return int
     */
    public function getHeight() : int {
        return $this->height;
    }

    /**
     * Command to resize contain the image
     *
     * Uses the behavior of (@see Image::contain()}.
     * @api
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function resizeContain(int $width, int $height) : ImageItem {
        $this->width = $width;
        $this->height = $height;
        $this->version = $width . 'x' . $height . '_contain';
        $this->resize_mode = self::RESIZE_MODE_CONTAIN;

        return $this;
    }

    /**
     * Command to resize cover the image
     *
     * Uses the behavior of (@see Image::cover()}.
     * @api
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function resizeCover(int $width, int $height) : ImageItem {
        $this->width = $width;
        $this->height = $height;
        $this->version = $width . 'x' . $height . '_cover';
        $this->resize_mode = self::RESIZE_MODE_COVER;

        return $this;
    }


    /**
     * Process image.
     *
     * This method is used by {@see ImageItem::generate()} and will be overriden
     * if you want to add more functionality to this object.
     * @api
     * @param Image $image
     */
    protected function process(Image $image) {
    }

    /**
     * Generate the image.
     *
     * If the target extension is {@see ImageItem::setTargetExtension() forced to jpg}
     * then the generated image is {@see Image::optimizePhoto() optimized as photo}/
     * @api
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

        if ( $this->resize_mode == self::RESIZE_MODE_CONTAIN ) {
            $img->contain(new Size($this->width, $this->height));

        } else if ( $this->resize_mode == self::RESIZE_MODE_COVER ) {
            $img->cover(new Size($this->width, $this->height));
        }

        if ( $this->target_extension == 'jpg') {
            $img->optimizePhoto();
        } else if ( $this->target_extension == 'png' ) {
            $img->optimizeLossless();
        } else {
            $img->optimize();
        }

        $this->width = $img->getImagickImage()->getImageWidth();
        $this->height = $img->getImagickImage()->getImageHeight();


        $img->writeImage($manager->prepareCacheFile($this));
    }

    /**
     * Stores additional data
     *
     * Stores the dimension of the image as an array with width and height
     * @return array
     */
    public function getAdditionalData(): array
    {
        return [
            'width' => $this->getWidth(),
            'height' => $this->getHeight()
        ];
    }

}
