<?php
/**
 * @filesource modules/repair/controllers/setup.php
 *
 
 */

namespace Repair\Setup;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=repair-setup
 
 */
class Controller extends \Gcms\Controller
{
    /**
     * รายการแจ้งซ่อม
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // ข้อความ title bar
        $this->title = Language::trans('{LNG_List of} {LNG_Technical Service}');
        // เลือกเมนู
        $this->menu = 'repair';
        // สมาชิก
        $login = Login::isMember();
        // สามารถจัดการรายการซ่อมได้, ช่างซ่อม
        if (Login::checkPermission($login, array('can_manage_technical', 'can_repair'))) { //can_manage_repair
            // แสดงผล
            $section = Html::create('section', array(
                'class' => 'content_bg',
            ));
            // breadcrumbs
            $breadcrumbs = $section->add('div', array(
                'class' => 'breadcrumbs',
            ));
            $ul = $breadcrumbs->add('ul');
            $ul->appendChild('<li><span class="icon-tools">{LNG_Module}</span></li>');
            $ul->appendChild('<li><span>{LNG_Technical Service}</span></li>');
            $ul->appendChild('<li><span>{LNG_Transaction history}</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-list">'.$this->title.'</h2>',
            ));
            // แสดงฟอร์ม
            $section->appendChild(\Repair\Setup\View::create()->render($request, $login));
            // คืนค่า HTML
            return $section->render();
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }
}
