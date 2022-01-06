<?php
/**
 * @filesource Gcms/Router.php
 *
 
 */

namespace Gcms;

/**
 * Router Class สำหรับ GCMS
 
 */
class Router extends \Kotchasan\Router
{
    /**
     * กฏของ Router สำหรับการแยกหน้าเว็บไซต์
     *
     * @var array
     */
    protected $rules = array(
        // api.php/<modules>/<method>/<action>
        '/api\.php\/([a-z0-9]+)\/([a-z]+)\/([a-z]+)/i' => array('module', 'method', 'action'),
    );
}
