<?php
/**
 * @filesource modules/repair/controllers/repairstatus.php
 *
 
 */

namespace Repair\Repairstatus;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=repair-repairstatus
 
 */
class Controller extends \Gcms\Controller
{
    /**
     * สถานะการซ่อม
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // ข้อความ title bar
        $this->title = Language::get('Repair status');
        // เลือกเมนู
        $this->menu = 'settings';
        // สามารถตั้งค่าระบบได้
        if (Login::checkPermission(Login::isMember(), 'can_config')) {
            // แสดงผล
            $section = Html::create('section', array(
                'class' => 'content_bg',
            ));
            // breadcrumbs
            $breadcrumbs = $section->add('div', array(
                'class' => 'breadcrumbs',
            ));
            $ul = $breadcrumbs->add('ul');
            $ul->appendChild('<li><span class="icon-tools">{LNG_Settings}</span></li>');
            $ul->appendChild('<li><span>{LNG_Technical Service}</span></li>');
            $ul->appendChild('<li><span>{LNG_Technical Service status}</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-valid">'.$this->title.'</h2>',
            ));
            // menu
            $section->appendChild(\Index\Tabmenus\View::render($request, 'settings', 'repair'));
            // แสดงฟอร์ม
            $section->appendChild(\Repair\Repairstatus\View::create()->render($request));
            // คืนค่า HTML
            return $section->render();
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }
}
