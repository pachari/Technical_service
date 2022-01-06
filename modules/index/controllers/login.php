<?php
/**
 * @filesource modules/index/controllers/login.php
 *
 
 */

namespace Index\Login;

use Kotchasan\Http\Request;

/**
 * สำหรับแสดงกรอบ Login
 
 */
class Controller extends \Gcms\Controller
{
    /**
     * จัดการกรอบ login
     *
     * @param Request $request
     * @param array $login
     *
     * @return string
     */
    public static function init(Request $request, $login)
    {
        if ($login) {
            return \Index\Login\View::member($request, $login);
        } else {
            return \Index\Login\View::login($request);
        }
    }
}
