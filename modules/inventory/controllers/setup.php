<?php
/**
 * @filesource modules/inventory/controllers/setup.php
 *
 
 */

namespace Inventory\Setup;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=inventory-setup
 
 */
class Controller extends \Gcms\Controller
{
    /**
     * ตารางรายการ สินค้า
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // ข้อความ title bar
        $this->title = Language::trans('{LNG_List of} {LNG_Inventory}');
        // เลือกเมนู
        $this->menu = 'types';
        // สามารถบริหารจัดการได้
        if (Login::checkPermission(Login::isMember(), 'can_manage_inventory')) {
            // แสดงผล
            $section = Html::create('section', array(
                'class' => 'content_bg',
            ));
            // breadcrumbs
            $breadcrumbs = $section->add('div', array(
                'class' => 'breadcrumbs',
            ));
            $ul = $breadcrumbs->add('ul');
            $ul->appendChild('<li><span class="icon-product">{LNG_Settings}</span></li>');
            $ul->appendChild('<li><span>{LNG_Inventory}</span></li>');
            $ul->appendChild('<li><span>{LNG_Transaction history}</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-list">'.$this->title.'</h2>',
            ));
            // menu
            $section->appendChild(\Index\Tabmenus\View::render($request, 'settings', 'inventory'));
            // แสดงตาราง
            $section->appendChild(\Inventory\Setup\View::create()->render($request));
            // คืนค่า HTML
            return $section->render();
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }
}
