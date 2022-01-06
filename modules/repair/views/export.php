<?php
/**
 * @filesource modules/repair/views/export.php
 *
 
 */

namespace Repair\Export;

use Kotchasan\Template;

/**
 * module=repair-export
 
 */
class View //extends \Gcms\View
{
    /**
     * ส่งออกข้อมูลเป็น HTML หรือ หน้าสำหรับพิมพ์.     *
     * @param array $content
     */
    public static function toPrint($content)
    {

        $template = Template::createFromFile(ROOT_PATH.'modules/repair/template/export.html'); 
        $template->add($content);
        echo $template->render();
    }
}
