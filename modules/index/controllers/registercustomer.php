<?php
/**
 * @filesource modules/index/controllers/registercustomer.php
 *
 *
 */

namespace Index\RegisterCustomer;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=registercustomer
 *
 */
class Controller extends \Gcms\Controller
{
    /**
     * ลงทะเบียนลูกค้าใหม่
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // ข้อความ title bar
        $this->title = Language::get('Create new customer');
        // เลือกเมนู
        $this->menu = 'customer';
        // แอดมิน, ไม่ใช่สมาชิกตัวอย่าง
        if (Login::notDemoMode(Login::isAdmin())) {
            // แสดงผล
            $section = Html::create('section', array(
                'class' => 'content_bg',
            ));
            // breadcrumbs
            $breadcrumbs = $section->add('div', array(
                'class' => 'breadcrumbs',
            ));
            $ul = $breadcrumbs->add('ul');
            $ul->appendChild('<li><span class="icon-product">{LNG_Customer}</span></li>');
            $ul->appendChild('<li><a href="{BACKURL?module=customer&id=0}">{LNG_Customer list}</a></li>');
            $ul->appendChild('<li><span>{LNG_Register Customer}</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-register">'.$this->title.'</h2>',
            ));
            // แสดงฟอร์ม
            $section->appendChild(\Index\RegisterCustomer\View::create()->render($request));
            // คืนค่า HTML
            return $section->render();
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }
}
