<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 25-05-18
 * Time: 23:13
 */

namespace edwrodrig\static_generator\html;

/**
 * Class Favicon
 *
 * Function to set the favicons header links.
 * The supported sizes are {@see Favicon::setIcon16x16() 16x16}, {@see Favicon::setIcon24x24() 24x24}, {@see Favicon::setIcon32x32() 32x32},
 * {@see Favicon::setIcon48x48() 48x48} and {@see Favicon::setIcon64x64() 64x64}
 * This class is made to be used inside the head section of a html document
 * ```
 * <head>
 * <?php (new Favicon)->setIcon16x16('some_icon.png')->print() ?>
 * </head>
 * ``
 * @package edwrodrig\static_generator\html
 */
class Favicon
{
    /**
     * The array that holds the icons
     * @var array
     */
    private $icons = [];

    /**
     * Set the 16x16 icon
     *
     * @api
     * @param string $icon
     * @return Favicon
     */
    public function setIcon16x16(string $icon) : Favicon  {
        $this->icons[16] = $icon;
        return $this;
    }

    /**
     * Set the 24x24 icon
     * @api
     * @param string $icon
     * @return Favicon
     */
    public function setIcon24x24(string $icon) : Favicon  {
        $this->icons[24] = $icon;
        return $this;
    }

    /**
     * Set the 32x32 icon
     *
     * @api
     * @param string $icon
     * @return Favicon
     */
    public function setIcon32x32(string $icon) : Favicon  {
        $this->icons[32] = $icon;
        return $this;
    }

    /**
     * Set the 48x48 icon
     *
     * @api
     * @param string $icon
     * @return Favicon
     */
    public function setIcon48x48(string $icon) : Favicon  {
        $this->icons[48] = $icon;
        return $this;
    }

    /**
     * Set the 64x64 icon
     *
     * @api
     * @param string $icon
     * @return Favicon
     */
    public function setIcon64x64(string $icon) : Favicon  {
        $this->icons[64] = $icon;
        return $this;
    }

    /**
     * Print the favicons links
     * @api
     */
    public function print() {
        foreach ( $this->icons as $size => $href ) :?>
            <link rel="shortcut icon" sizes="<?=$size?>x<?=$size?>" href="<?=$href?>">
        <?php endforeach;
    }

}