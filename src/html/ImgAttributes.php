<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\html;

/**
 * Class ImgAttributes
 *
 * Convenience function to print image attributes
 * ```
 * <img <?=ImgAttributes::create(['src' => 'image.jpg', 'alt'=> 'my photo'])?>>
 * ```
 * @package edwrodrig\static_generator\html
 * @see https://www.w3schools.com/tags/tag_a.asp
 */
class ImgAttributes
{
    /**
     * @var string|null
     */
    private $src = null;

    /**
     * @var null|string
     */
    private $alt = false;

    /**
     * @var null|string
     */
    private $title = null;

    /**
     * @var null|int
     */
    private $width = null;

    /**
     * @var null|int
     */
    private $height = null;



    /**
     * Specifies the URL of the image source
     *
     * Possibilities
     * * anchor – #top
     * * mail – mailto:edwrodrig@gmail.com
     * * telephone call – tel:0036012345678
     * * absolute url – https://edwin.cl
     * * relative url –'edwin/index.html
     * @param $src
     * @see https://www.w3schools.com/tags/att_a_href.asp
     * @return AAttributes
     */
    public function setSrc(?string $src) : ImgAttributes {
        $this->src = $src;
        return $this;
    }


    /**
     * The alternate text content
     *
     * The required alt attribute specifies an alternate text for an image, if the image cannot be displayed.
     * Guidelines for the alt text:
     * * The text should describe the image if the image contains information
     * * The text should explain where the link goes if the image is inside an <a> element
     * * Use alt="" if the image is only for decoration
     * @see https://www.w3schools.com/tags/att_img_alt.asp
     * @see ImgAttributes::setTitle() to set a tooltip
     * @param bool|string $alt
     * @return ImgAttributes
     */
    public function setAlt(?string $alt) : ImgAttributes {
        $this->alt = $alt;
        return $this;
    }

    /**
     * This will be the tooltip when you hover your mouse above the link.
     *
     * @param null|string $title
     * @return ImgAttributes
     */
    public function setTitle(?string $title) : ImgAttributes {
        $this->title = $title;
        return $this;
    }

    /**
     * The height attribute specifies the height of an image, in pixels.
     *
     * @param int|null $height
     * @return ImgAttributes
     * @see https://www.w3schools.com/tags/att_img_height.asp
     */
    public function setHeight(?int $height) : ImgAttributes {
        $this->height = $height;
        return $this;
    }

    /**
     * The height attribute specifies the height of an image, in pixels.
     *
     * @param int|null $width
     * @return ImgAttributes
     * @see https://www.w3schools.com/tags/att_img_height.asp
     */
    public function setWidth(?int $width) : ImgAttributes {
        $this->width = $width;
        return $this;
    }

    /**
     * Convert to string
     * @return string
     */
    public function __toString() : string {
        $parts = [];
        Attributes::addPart('src', $this->src,$parts);
        Attributes::addPart('title', $this->title,$parts);
        Attributes::addPart('alt', $this->alt,$parts);
        Attributes::addPart('width', $this->width,$parts);
        Attributes::addPart('height', $this->height,$parts);

        return implode(' ', $parts);
    }

    /**
     * Create the attributes from array
     *
     * Key values
     * * {@see ImgAttributes::setTitle() title}
     * * {@see ImgAttributes::setSrc() src}
     * * {@see ImgAttributes::setAlt() alt}
     * * {@see ImgAttributes::setWidth() width}
     * * {@see ImgAttributes::setHeight() height}
     * @param array $attributes
     * @return ImgAttributes
     */
    public static function create(array $attributes) : ImgAttributes {
        $a = new self;
        $a
            ->setSrc($attributes['src'] ?? null)
            ->setTitle($attributes['title'] ?? null)
            ->setAlt($attributes['alt'] ?? null)
            ->setWidth($attributes['width'] ?? null)
            ->setHeight($attributes['height'] ?? null);

        return $a;
    }

}