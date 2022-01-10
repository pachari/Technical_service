<?php
/**
 * @filesource modules/index/controllers/customer.php
 *
 */

namespace Index\Customer;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=customer
 *
 */
class Controller extends \Gcms\Controller
{
    /**
     * รายชื่อลูกค้า
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // ข้อความ title bar
        $this->title = Language::get('Customer list');
        // เลือกเมนู
        $this->menu = 'cust';
        // แอดมิน, ไม่ใช่สมาชิกตัวอย่าง
        if (Login::checkPermission(Login::isMember(), 'can_manage_customer')) {
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
            $ul->appendChild('<li><span>'.$this->title.'</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-product">'.$this->title.'</h2>',
            ));
            // แสดงตาราง
            $section->appendChild(\Index\Customer\View::create()->render($request));
            // คืนค่า HTML
            return $section->render();
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }
}
