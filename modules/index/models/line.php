<?php
/**
 * @filesource modules/index/models/line.php
 *
 
 */

namespace Index\Line;

use Gcms\Line;
use Kotchasan\Http\Request;

/**
 * module=line
 
 */
class Model extends \Kotchasan\KBase
{
    /**
     * ทดสอบการส่ง Line
     *
     * @param Request $request
     */
    public function test(Request $request)
    {
        // referer
        if ($request->isReferer() && $request->isAjax()) {
            // ทดสอบส่งข้อความ Line
            Line::send(strip_tags(self::$cfg->web_title), $request->post('id')->quote());
        }
    }
}
