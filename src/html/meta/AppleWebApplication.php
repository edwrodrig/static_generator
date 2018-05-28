<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\html\meta;

use edwrodrig\static_generator\util\Util;

/**
 * Class AppleWebApplication
 *
 * A class to implementing {@see https://developer.apple.com/library/content/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html web applications tags} recommended by apple.
 * This class is made to be used inside the head section of a html document
 * ```
 * <head>
 * <?php (new AppleWebApplication)->setIcon16x16('some_icon.png')->print() ?>
 * </head>
 * ```
 * @package edwrodrig\static_generator\html
 */
class AppleWebApplication
{

    /**
     * @var string[]
     */
    private $icons = [];

    /**
     * @var string|null
     */
    private $startup_image = null;

    /**
     * @var string|null
     */
    private $status_bar_style = null;

    /**
     * @var bool
     */
    private $web_capable = false;

    /**
     * @var string|null
     */
    private $title = null;


    /**
     * Set a icon of 72x72.
     *
     * @param string $icon
     * @return AppleWebApplication
     */
    public function setIcon72x72(string $icon) : AppleWebApplication {
        $this->icons[72] = $icon;
        return $this;
    }

    /**
     * Set a icon of 152x152
     *
     * @param string $icon
     * @return AppleWebApplication
     */
    public function setIcon152x125(string $icon) : AppleWebApplication {
        $this->icons[152] = $icon;
        return $this;
    }

    /**
     * Set a icon of 167x167.
     *
     * @param string $icon
     * @return AppleWebApplication
     */
    public function setIcon167x167(string $icon) : AppleWebApplication {
        $this->icons[167] = $icon;
        return $this;
    }

    /**
     * Set a icon of 180x180.
     *
     * User for retina displays
     * @param string $icon
     * @return AppleWebApplication
     */
    public function setIcon180x180(string $icon) : AppleWebApplication {
        $this->icons[180] = $icon;
        return $this;
    }

    /**
     * Set the startup image.
     *
     * Is seems that is some splash image that is show while the page is loading.
     * When this is not defined it seems that apple devices show a last screenshow of the app. But I do not have a apple device to test it.
     *
     * @param string $image
     * @return AppleWebApplication
     */
    public function setStartupImage(string $image) : AppleWebApplication {
        $this->startup_image = $image;
        return $this;
    }

    /**
     * Set if the application if web capable
     *
     * @param bool $web_capable
     * @return AppleWebApplication
     */
    public function setWebCapable(bool $web_capable) : AppleWebApplication {
        $this->web_capable = $web_capable;
        return $this;
    }

    /**
     * Set the status bar style
     *
     * The style generaly is a color, for example 'black'
     * @see https://developer.apple.com/documentation/uikit/uinavigationbar
     * @param string $style
     * @return AppleWebApplication
     */
    public function setStatusBarStyle(string $style) : AppleWebApplication {
        $this->status_bar_style = $style;
        return $this;
    }

    /**
     * Set the title of the app.
     *
     * Generally match with the page name.
     * @param string $title
     * @return AppleWebApplication
     */
    public function setTitle(string $title) : AppleWebApplication {
        $this->title = $title;
        return $this;
    }

    /**
     * Print the web application tags.
     *
     * Ignore which are not set
     */
    public function print() {
        if ( $this->web_capable ) {
            echo Util::sprintfOrEmpty('<meta name="apple-mobile-web-app-capable" content="yes">');
        }

        echo Util::sprintfOrEmpty('<meta name="apple-mobile-web-app-title" content="%s">', $this->title);
        echo Util::sprintfOrEmpty('<meta name="apple-mobile-web-app-status-bar-style" content="%s">', $this->status_bar_style);
        echo Util::sprintfOrEmpty('<link rel="apple-touch-startup-image" href="%s">', $this->startup_image);


        foreach ( $this->icons as $size => $href )  {
            printf('<link rel="apple-touch-icon" sizes="%dx%d" href="%s">', $size, $size, $href);
        }

    }

}