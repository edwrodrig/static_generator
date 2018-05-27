<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-05-18
 * Time: 10:27
 */

namespace edwrodrig\static_generator\html;
use edwrodrig\static_generator\util\Util;

/**
 * Class TwitterCard
 * @package edwrodrig\static_generator\htmlt
 * @see https://developer.twitter.com/en/docs/tweets/optimize-with-cards/guides/troubleshooting-cards
 * @see https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/summary-card-with-large-image
 */
class TwitterCardSummary
{

    /**
     * @var null|string
     */
    private $site = null;

    /**
     * @var null|string
     */
    private $title = null;

    /**
     * @var null|string
     */
    private $description = null;

    /**
     * @var null|string
     */
    private $image = null;

    /**
     * @var null|string
     */
    private $image_alt = null;


    /**
     * @var null|string
     */
    private $creator = null;

    /**
     * @var bool
     */
    private $is_large_image = false;

    /**
     * Set the twitter username of the card should be attributed to
     *
     * Should add the leading @
     * Website Attribution:
     * Indicates the Twitter account for the website or platform on which the content was published.
     * Note that a service may set separate Twitter accounts for different pages/sections of their website,
     * and the most appropriate Twitter account should be used to provide the best context for the user.
     * For example, nytimes.com may set the the website attribution to “@nytimes” for front page articles, and “@NYTArts”
     * for articles in the Arts & Entertainment section.
     * @api
     * @param null|string $user
     * @return $this
     */
    public function setSite(?string $user) : TwitterCardSummary {
        $this->site = $user;
        return $this;

    }

    /**
     * Set if this summary is large image.
     *
     * @see https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/summary-card-with-large-image
     * @param bool $is_large_image
     * @return TwitterCardSummary
     */
    public function setLargeImage(bool $is_large_image) : TwitterCardSummary {
        $this->is_large_image = $is_large_image;
        return $this;
    }

    /**
     * Set the creator of this summary.
     *
     * The creator is the author of the content instead the owner of the page.
     * In the case of a news, this should be the twitter account of the writer.
     * Only works in when this card is {@see TwitterCardSummary::setLargeImage() large image}
     * @param null|string $creator
     * @return TwitterCardSummary
     */
    public function setCreator(?string $creator) : TwitterCardSummary {
        $this->creator = $creator;
        return $this;
    }

    /**
     * A concise title for the related content.
     *
     * Platform specific behaviors:
     * * iOS, Android: Truncated to two lines in timeline and expanded Tweet
     * * Web: Truncated to one line in timeline and expanded Tweet
     * When not set, Fallbacks to {@see OpenGraph::setTitle()}
     * @api
     * @param null|string $title
     * @return $this
     */
    public function setTitle(?string $title) : TwitterCardSummary {
        $this->title = $title;
        return $this;
    }

    /**
     * A description that concisely summarizes the content as appropriate for presentation within a Tweet.
     *
     * You should not re-use the title as the description or use this field to describe the general services provided by the website.
     * Platform specific behaviors:
     * * iOS, Android: Not displayed
     * * Web: Truncated to three lines in timeline and expanded Tweet
     * When not set, fallbacks to {@see OpenGraph::setDescription()}
     * @param null|string $description
     * @return $this
     */
    public function setDescription(?string $description) : TwitterCardSummary {
        $this->description = $description;
        return $this;
    }

    /**
     * A URL to a unique image representing the content of the page.
     *
     * You should not use a generic image such as your website logo, author photo, or other image that spans multiple pages.
     * Images for this Card support an aspect ratio of 1:1 with minimum dimensions of 144x144
     * or maximum of 4096x4096 pixels.
     * Images must be less than 5MB in size.
     * The image will be cropped to a square on all platforms. JPG, PNG, WEBP and GIF formats are supported.
     * Only the first frame of an animated GIF will be used. SVG is not supported.
     * When not set, Fallbacks to {@see OpenGraph::setImage()}
     * @api
     * @param null|string $image
     * @return $this
     */
    public function setImage(?string $image) : TwitterCardSummary {
        $this->image = $image;
        return $this;
    }

    /**
     * Set a text description of the image conveying the essential nature of an image to users who are visually impaired.
     *
     * Maximum 420 characters.
     * @api
     * @param null|string $alt
     * @return $this
     */
    public function setImageAlt(?string $alt) : TwitterCardSummary {
        $this->image_alt= $alt;
        return $this;
    }


    public function print() {
        if ( $this->is_large_image ) {
            echo Util::sprintfOrEmpty('<meta name="twitter:card" content="summary_large_image"/>');
            echo Util::sprintfOrEmpty('<meta name="twitter:creator" content="%s"/>', $this->creator);
        } else {
            echo Util::sprintfOrEmpty('<meta name="twitter:card" content="summary"/>');
        }

        echo Util::sprintfOrEmpty('<meta name="twitter:site" content="%s"/>', $this->site);
        echo Util::sprintfOrEmpty('<meta name="twitter:title" content="%s"/>', $this->title);
        echo Util::sprintfOrEmpty('<meta name="twitter:description" content="%s"/>', $this->description);
        echo Util::sprintfOrEmpty('<meta name="twitter:image" content="%s"/>', $this->image);

        if ( !is_null($this->image) ) {
            echo Util::sprintfOrEmpty('<meta name="twitter:image:alt" content="%s"/>', $this->image_alt);
        }

    }
}