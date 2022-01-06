<?php
/**
 * @filesource modules/repair/controllers/detail.php
 *
 
 */

namespace Repair\Detail;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=repair-detail
 
 */
class Controller extends \Gcms\Controller
{
    /**
     * รายละเอียดการซ่อม
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // อ่านข้อมูลรายการที่ต้องการ
        $index = \Repair\Detail\Model::get($request->request('id')->toInt());
        // ข้อความ title bar
        $this->title = Language::get('Technical Service job description');
        // เลือกเมนู
        $this->menu = 'repair';
        // สมาชิก
        $login = Login::isMember();
        // ผู้ส่งซ่อม หรือ สามารถรับเครื่องซ่อมได้
        if ($index && ($login['id'] == $index->customer_id || Login::checkPermission($login, array('can_manage_repair', 'can_repair')) || Login::checkPermission($login, array('approve_manage_repair', 'approve_repair')) )) {
            // แสดงผล
            $section = Html::create('section', array(
                'class' => 'content_bg',
            ));
            // breadcrumbs
            $breadcrumbs = $section->add('div', array(
                'class' => 'breadcrumbs',
            ));
            $ul = $breadcrumbs->add('ul');
            $ul->appendChild('<li><span class="icon-tools">{LNG_Technical Service system}</span></li>');
            $ul->appendChild('<li><a href="{BACKURL?module=repair-setup&id=0}">{LNG_Technical Service list}</a></li>');
            $ul->appendChild('<li><span>'.$index->topic.'</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-write">'.$this->title.'</h2>',
            ));
            // แสดงฟอร์ม
            $section->appendChild(\Repair\Detail\View::create()->render($index, $login));
            // คืนค่า HTML
            return $section->render();
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }
}
