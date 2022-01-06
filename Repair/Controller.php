<?php
/**
 * @filesource Kotchasan/Controller.php
 *
 
 */

namespace Kotchasan;

/**
 * Controller base class
 
 */
class Controller extends \Kotchasan\KBase
{
    /**
     * create class
     *
     * @return static
     */
    public static function create()
    {
        return new static;
    }
}
