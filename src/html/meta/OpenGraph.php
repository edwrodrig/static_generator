<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 27-05-18
 * Time: 9:52
 */

namespace edwrodrig\static_generator\html\meta;
use DateTime;
use edwrodrig\static_generator\util\Util;


/**
 * Class OpenGraph
 *
 * Class to implement OpenGraph meta tags, focused in the compatibility with facebook
 * @package edwrodrig\static_generator\html
 * @see http://ogp.me/ Opengraph page
 * @see https://developers.facebook.com/docs/sharing/opengraph
 * @see https://developers.facebook.com/docs/sharing/opengraph/object-properties?locale=en_US#standard
 * @see https://developers.facebook.com/tools/debug/ Debug tool
 * @see https://developers.facebook.com/docs/sharing/best-practices/#tags
 * @see https://developers.facebook.com/docs/sharing/best-practices/#images
 */
class OpenGraph
{
    /**
     *
     * The URL of the object, which acts as the canonical URL.
     * Usually the same URL as the page where property tags are placed.
     * It shouldn't include any session variables, user identifying parameters, or counters.
     * Always use the canonical URL for this tag, or likes and shares will be spread across all of the variations of the URL.
     * @param string $url
     * @return OpenGraph
     */
    public function setUrl(string $url): OpenGraph
    {
        $this->url = $url;
        return $this;
    }

    /**
     * The title, headline or name of the object.
     * @param string $title
     * @return OpenGraph
     */
    public function setTitle(string $title): OpenGraph
    {
        $this->title = $title;
        return $this;
    }

    /**
     * A short description or summary of the object.
     * @param string $description
     * @return OpenGraph
     */
    public function setDescription(string $description): OpenGraph
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Find out the type of your object in the Action Type section of App Dashboard.
     * Select the object and find its og:type under Advanced.
     * Once an object is published in a story its type can't be changed.
     * @param string $type
     * @return OpenGraph
     */
    public function setType(string $type): OpenGraph
    {
        $this->type = $type;
        return $this;
    }

    /**
     * The URL of the image for your object.
     *
     * It should be at least 600x315 pixels, but 1200x630 or larger is preferred (up to 5MB).
     * Stay close to a 1.91:1 aspect ratio to avoid cropping.
     * Game icons should be square and at least 600x600 pixels.
     * You can include multiple og:image tags if you have multiple resolutions available.
     * If you update the image after publishing, use a new URL because images are cached based on the URL and might not update otherwise.
     * @param null|string $image
     * @return OpenGraph
     */
    public function setImage(?string $image): OpenGraph
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Set the image width
     * @param int|null $image_width
     * @return OpenGraph
     */
    public function setImageWidth(?int $image_width): OpenGraph
    {
        $this->image_width = $image_width;
        return $this;
    }

    /**
     * Set image height
     *
     * @param int|null $image_height
     * @return OpenGraph
     */
    public function setImageHeight(?int $image_height): OpenGraph
    {
        $this->image_height = $image_height;
        return $this;
    }

    /**
     * Set locale
     *
     * The language locale that object properties use. The default is en_US.
     * @param null|string $locale
     * @return OpenGraph
     */
    public function setLocale(?string $locale): OpenGraph
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Set de determiner
     *
     * The word that appears before the object in a story (such as "an Omelette").
     * This value should be a string that is a member of the Enum {a, an, the, "", auto}.
     * When 'auto' is selected, Facebook will choose between 'a' or 'an'. Default is blank.
     * Is the spanish articulo definido-indefinido
     * @param null|string $determiner
     * @return OpenGraph
     */
    public function setDeterminer(?string $determiner): OpenGraph
    {
        $this->determiner = $determiner;
        return $this;
    }

    /**
     * Set the date of last actualization
     *
     * When the object was last updated.
     * @param DateTime|null $update_time
     * @return OpenGraph
     */
    public function setUpdateTime(?DateTime $update_time): OpenGraph
    {
        $this->update_time = $update_time;
        return $this;
    }

    /**
     * Set additional link
     *
     * Used to supply an additional link that shows related content to the object.
     * @param null|string $see_also
     * @return OpenGraph
     */
    public function setSeeAlso(?string $see_also): OpenGraph
    {
        $this->see_also = $see_also;
        return $this;
    }

    /**
     * Set if is rich attachment
     *
     * When "true", stories published will render with rich metadata such as the title, description, author, site name, and preview image.
     * @param bool $rich_attachment
     * @return OpenGraph
     */
    public function setRichAttachment(bool $rich_attachment): OpenGraph
    {
        $this->rich_attachment = $rich_attachment;
        return $this;
    }

    /**
     * Set the time to live
     *
     * Seconds until this page should be re-scraped. Use this to rate limit the Facebook content crawlers. The minimum allowed value is 345600 seconds (4 days); if you set a lower value, the minimum will be used. If you do not include this tag, the ttl will be computed from the "Expires" header returned by your web server, otherwise it will default to 7 days.
     * @param null|int $time_to_live
     * @return OpenGraph
     */
    public function setTimeToLive(?int $time_to_live): OpenGraph
    {
        $this->time_to_live = $time_to_live;
        return $this;
    }

    public function print() {
        echo Util::sprintfOrEmpty('<meta property="og:url" content="%s"/>', $this->url);
        echo Util::sprintfOrEmpty('<meta property="og:title" content="%s"/>', $this->title);
        echo Util::sprintfOrEmpty('<meta property="og:description" content="%s"/>', $this->description);
        echo Util::sprintfOrEmpty('<meta property="og:image" content="%s"/>', $this->image);
        echo Util::sprintfOrEmpty('<meta property="og:locale" content="%s"/>', $this->locale);
        echo Util::sprintfOrEmpty('<meta property="og:determiner" content="%s"/>', $this->determiner);
        echo Util::sprintfOrEmpty('<meta property="og:update_time" content="%s"/>', strval($this->update_time));
        echo Util::sprintfOrEmpty('<meta property="og:see_also" content="%s"/>', $this->see_also);
        echo Util::sprintfOrEmpty('<meta property="og:ttl" content="%s"/>', $this->time_to_live);

        if ( !is_null($this->image) ) {
            echo Util::sprintfOrEmpty('<meta property="og:image:height" content="%d"/>', $this->image_height);
            echo Util::sprintfOrEmpty('<meta property="og:image:width" content="%d"/>', $this->image_width);
        }
        if ( $this->rich_attachment )
            echo Util::sprintfOrEmpty('<meta property="og:rich_attachment" content="true"/>');
    }



    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string|null
     */
    private $image;

    /**
     * @var int|null
     */
    private $image_width;

    /**
     * @var int|null
     */
    private $image_height;

    /**
     * @var string|null
     */
    private $locale;

    /**
     * @var string|null
     */
    private $determiner;


    /**
     * @var DateTime|null
     */
    private $update_time;

    /**
     * @var string|null
     */
    private $see_also;

    /**
     * @var bool
     */
    private $rich_attachment =false;

    /**
     * @var int|null
     */
    private $time_to_live;



}