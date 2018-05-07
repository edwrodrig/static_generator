<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 06-05-18
 * Time: 12:16
 */

namespace edwrodrig\static_generator\util;


class GenerationType
{
    /**
     * Indicates a file that is generated as it is. That means just get it contents and copy to the output without any type of processing.
     */
    const RAW = 0;

    /**
     * Indicates a file that is processes as a php file but do not generate any output unless it explicitly indicated it.
     */
    const PROCESS = 1;

    /**
     * Indicates file that is processes as a {@see PageTemplateInstance template}.
     */
    const TEMPLATE = 2;

    /**
     * A file that
     */
    const SCSS = 3;


    const PHP = 4;

    /**
     * Indicated file that must be ignored
     */
    const IGNORE = -1;
}