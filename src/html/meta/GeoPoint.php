<?php
declare(strict_types=1);

namespace edwrodrig\static_generator\html\meta;

use edwrodrig\static_generator\util\Util;

/**
 * Class GeoPoint
 *
 * Use the geopoint complex type when defining objects that specify spatial information (ex: geographic location).
 * @package edwrodrig\static_generator\html\meta
 */
class GeoPoint
{

    /**
     * @var string|null
     */
    private ?string $longitude = null;

    /**
     * @var string|null
     */
    private ?string $latitude = null;

    /**
     * @var string|null
     */
    private ?string $altitude = null;

    /**
     * longitude
     *
     * Longitude as represented in decimal degrees format.
     * @param null|string $longitude
     * @return GeoPoint
     */
    public function setLongitude(?string $longitude): GeoPoint
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * latitude
     *
     * Latitude as represented in decimal degrees format.
     * @param null|string $latitude
     * @return GeoPoint
     */
    public function setLatitude(?string $latitude): GeoPoint
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * altitude
     *
     * Altitude of location in feet.
     * @param null|string $altitude
     * @return GeoPoint
     */
    public function setAltitude(?string $altitude): GeoPoint
    {
        $this->altitude = $altitude;
        return $this;
    }

    public function print() {
        echo Util::sprintfOrEmpty('<meta property="place:location:longitude"  content="%s">', $this->longitude);
        echo Util::sprintfOrEmpty('<meta property="place:location:latitude"  content="%s">', $this->latitude);
        echo Util::sprintfOrEmpty('<meta property="place:location:altitude"  content="%s">', $this->altitude);
    }


}