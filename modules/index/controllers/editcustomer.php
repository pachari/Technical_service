<?php
/**
 * @filesource modules/index/controllers/editcustomer.php
 *
 */

namespace Index\Editcustomer;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=editcustomer
 *
 */
class Controller extends \Gcms\Controller
{
    /**
     * แก้ไขข้อมูลส่วนตัวสมาชิก
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // ข้อความ title bar
        $this->title = Language::get('Editing your customer');
        // เลือกเมนู
        $this->menu = 'customer';
        // สมาชิก, ไม่ใช่สมาชิกตัวอย่าง
        if ($login = Login::checkPermission(Login::isMember(), 'can_manage_customer')) {
            // อ่านข้อมูลสมาชิก
            $Cus = \Index\Editcustomer\Model::get($request->request('id')->toInt());
            // ตัวเอง, แอดมินแก้ไขได้ทุกคน ยกเว้น ID 1 
           // if (Login::notDemoMode(Login::isAdmin())) {
              
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
                $ul->appendChild('<li><span>{LNG_Edit}</span></li>');
                $section->add('header', array(
                    'innerHTML' => '<h2 class="icon-product">'.$this->title.'</h2>',
                ));
                // แสดงฟอร์ม
               $section->appendChild(\Index\Editcustomer\View::create()->render($request, $Cus , $login));
                // คืนค่า HTML
                return $section->render();
          //  }
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }
}
