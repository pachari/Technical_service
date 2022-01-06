<?php
/**
 * @filesource modules/index/controllers/reportg.php
 *
 
 */

namespace Index\Reportg;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=reportg
 
 */
class Controller extends \Gcms\Controller
{
    /**
     * รายงานรูปแบบกราฟ
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        
        // ข้อความ title bar
        $this->title = Language::trans('{LNG_report}{LNG_Graph-report}');
        // เลือกเมนู
        $this->menu = 'report';
        // สมาชิก
        $login = Login::isMember();
        // สามารถดูรายงาน
        if (Login::checkPermission($login, array('report'))) {
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
            $ul->appendChild('<li><span>{LNG_report}</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-list">'.$this->title.'</h2>',
            ));
            // แสดงกราฟ
            $section->appendChild(\index\Reportg\View::create()->render($request));
            // คืนค่า HTML
            return $section->render();
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }

   
}
