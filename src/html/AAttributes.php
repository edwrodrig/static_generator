<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\html;

/**
 * Class AAttributes
 *
 * Convenience function to print anchor attributes
 * ```
 * <a <?=AAttributes::create(['href' => 'https://www.edwin.cl', 'alt'=> 'my page'])?>></a>
 * ```
 * @package edwrodrig\static_generator\html
 * @see https://www.w3schools.com/tags/tag_a.asp
 */
class AAttributes
{
    /**
     * @var string|null
     */
    private $href = null;

    /**
     * @var bool|string
     */
    private $download = false;

    /**
     * @var null|string
     */
    private $rel = null;

    /**
     * @var null|string
     */
    private $title = null;

    /**
     * @var null|string
     */
    private $target = null;


    /**
     * Specifies the URL of the page the link goes to
     *
     * Possibilities
     * * anchor – #top
     * * mail – mailto:edwrodrig@gmail.com
     * * telephone call – tel:0036012345678
     * * absolute url – https://edwin.cl
     * * relative url –'edwin/index.html
     * @param $href
     * @see https://www.w3schools.com/tags/att_a_href.asp
     * @return AAttributes
     */
    public function setHref(?string $href) : AAttributes {
        $this->href = $href;
        return $this;
    }

    /**
     * This attribute specifies the relationship between the current and the linked documents.
     *
     * Possibilities:
     * * alternate – link to an alternate representation of the document (i.e. print page, translated or mirror)
     * * author – link to the author of the document
     * * bookmark – URL used for bookmarking
     * * external – the referenced document is not part of the same site as the current document
     * * help – link to a help document
     * * license – link to copyright information for the document
     * * next – link to the next document in the series
     * * nofollow – links to an unendorsed document, like a paid link. This way you can ask the Google search spider not to follow that link.
     * * noreferrer – the browser should not send an HTTP referer header if the user follows the hyperlink
     * * noopener – any browsing context created by following the hyperlink must not have an opener browsing context
     * * prev – previous document in a selection
     * * search – link to a search tool for the document
     * * tag – a keyword (tag) for the current document
     *
     * @see https://www.w3schools.com/tags/att_a_rel.asp
     * @see https://support.google.com/webmasters/answer/96569
     * @param string $rel
     * @return AAttributes
     */
    public function setRel(?string $rel) : AAttributes {
        $this->rel = $rel;
        return $this;
    }

    /**
     * The download value
     *
     * if true Download file when clicking on the link (instead of navigating to the file)
     * if a string alsoSpecify a value for the download attribute, which will be the new filename of the downloaded file
     * @see https://www.w3schools.com/tags/att_a_download.asp
     * @param bool|string $download
     * @return AAttributes
     */
    public function setDownload($download) : AAttributes {
        $this->download = $download;
        return $this;
    }

    /**
     * This will be the tooltip when you hover your mouse above the link.
     *
     * @param null|string $title
     * @return AAttributes
     */
    public function setTitle(?string $title) : AAttributes {
        $this->title = $title;
        return $this;
    }

    /**
     * Set the target of the document.
     *
     * Generally is used when the link opens a new window, a general request from clients.
     * Possibilities:
     * * _blank – Opens the linked document in a new window or tab
     * * _self – Opens the linked document in the same frame as it was clicked (this is default)
     * * _parent – Opens the linked document in the parent frame
     * * _top – Opens the linked document in the full body of the window
     * * framename – Opens the linked document in a named frame
     * @param string $target
     * @see https://www.w3schools.com/tags/att_a_target.asp
     * @return AAttributes
     */
    public function setTarget(?string $target) : AAttributes {
        $this->target = $target;
        return $this;
    }

    /**
     * Convert to string
     * @return string
     */
    public function __toString() : string {
        $parts = [];
        Attributes::addPart('href', $this->href,$parts);
        Attributes::addPart('title', $this->title,$parts);
        Attributes::addPart('rel', $this->rel,$parts);
        Attributes::addPart('download', $this->download,$parts);
        Attributes::addPart('target', $this->target,$parts);

        return implode(' ', $parts);
    }

    /**
     * Create the attributes from array
     *
     * Key values
     * * {@see AAttributes::setTitle() title}
     * * {@see AAttributes::setHref() href}
     * * {@see AAttributes::setRel() rel}
     * * {@see AAttributes::setDownload() download}
     * * {@see AAttributes::setTarget() target}
     * @param array $attributes
     * @return AAttributes
     */
    public static function create(array $attributes) : AAttributes {
        $a = new self;
        $a
            ->setTitle($attributes['title'] ?? null)
            ->setHref($attributes['href'] ?? null)
            ->setRel($attributes['rel'] ?? null)
            ->setDownload($attributes['download'] ?? false)
            ->setTarget($attributes['target'] ?? null);

        return $a;
    }

}