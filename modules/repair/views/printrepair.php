<?php
/**
 * @filesource modules/repair/views/printrepair.php
 *
 
 */

namespace Repair\Printrepair;

use Kotchasan\Template;

/**
 *  module=repair-printrepair
 
 */
class View //extends \Gcms\View
{
    /**
     * ส่งออกข้อมูลเป็น HTML หรือ หน้าสำหรับพิมพ์.     *
     * @param array $content
     */
    public static function toPrint($content)
    {
        $template = Template::createFromFile(ROOT_PATH.'modules/repair/template/printrepair.html'); 
        $template->add($content);
        echo $template->render();
    }
}
