<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-05-18
 * Time: 10:27
 */

namespace edwrodrig\static_generator\html\meta;
use edwrodrig\static_generator\util\Util;

/**
 * Class TwitterCard Application
 * @package edwrodrig\static_generator\htmlt
 * @see https://developer.twitter.com/en/docs/tweets/optimize-with-cards/guides/troubleshooting-cards
 * @see https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/app-card
 */
class TwitterCardApplication extends TwitterCardBase {
    /**
     * @var string|null
     */
    private $app_id_iphone;

    /**
     * @var string|null
     */
    private $app_id_ipad;

    /**
     * @var string|null
     */
    private $app_id_google_play;


    /**
     * Set de description of the app
     *
     * You can use this as a more concise description than what you may have on the app store. This field has a maximum of 200 characters
     * @param null|string $description
     * @return $this
     */
    public function setDescription(?string $description) : TwitterCardBase {
        return parent::setDescription($description);
    }

    /**
     * Set the app id for iphone
     *
     * String value, and should be the numeric representation of your app ID in the App Store (.i.e. “307234931”)
     * @param null|string $app_id
     * @return $this
     */
    public function setAppIdIphone(?string $app_id) : TwitterCardApplication {
        $this->app_id_iphone = $app_id;
        return $this;
    }

    /**
     * Set the app id for ipad
     *
     * String value, should be the numeric representation of your app ID in the App Store (.i.e. “307234931”)
     * @param null|string $app_id
     * @return $this
     */
    public function setAppIdIpad(?string $app_id) : TwitterCardApplication {
        $this->app_id_ipad = $app_id;
        return $this;

    }

    /**
     * Set the add id for android
     *
     * String value, and should be the numeric representation of your app ID in Google Play (.i.e. “com.android.app”)
     * @see https://support.bitly.com/hc/en-us/articles/230664607-What-is-an-App-ID-How-do-I-find-it- How can find the app id
     * @param null|string $app_id
     * @return $this
     */
    public function setAppIdGooglePlay(?string $app_id) : TwitterCardApplication {
        $this->app_id_google_play = $app_id;
        return $this;

    }

    /**
     * Print the image to HTML.
     */
    public function print() {
        parent::print();

        echo Util::sprintfOrEmpty('<meta name="twitter:card" content="app"/>');

        echo Util::sprintfOrEmpty('<meta name="twitter:app:id:iphone" content="%s"/>', $this->app_id_iphone);
        echo Util::sprintfOrEmpty('<meta name="twitter:app:id:ipad" content="%s"/>', $this->app_id_ipad);
        echo Util::sprintfOrEmpty('<meta name="twitter:app:id:googleplay" content="%s"/>', $this->app_id_google_play);

    }


}