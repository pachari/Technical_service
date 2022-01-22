<?php
/**
 * @filesource modules/repair/views/setup.php
 *
 
 */

namespace Repair\Setup;

use Gcms\Login;
use Kotchasan\DataTable;
use Kotchasan\Date;
use Kotchasan\Http\Request;
use Kotchasan\Language;
/**
 * module=repair-setup
 
 */
class View extends \Gcms\View
{
    /**
     * @var obj
     */
    private $statuses;
    /**
     * @var obj
     */
    private $operators;

    /**
     * รายการซ่อม (ช่างซ่อม)
     *
     * @param Request $request
     * @param array   $login
     *
     * @return string
     */
    public function render(Request $request, $login)
    {
        $params = array(
            'status' => $request->request('status', -1)->toInt(),
        );
        $isAdmin = Login::checkPermission($login, 'can_manage_technical'); //can_manage_repair
        // สถานะการซ่อม
        $this->statuses = \Repair\Status\Model::create();
        $this->operators = \Repair\Operator\Model::create();
        $operators = array();
        if ($isAdmin) {
            $operators[0] = '{LNG_all items}';
            $params['operator_id'] = $request->request('operator_id')->toInt();
        } else {
            $params['operator_id'] = array(0, $login['id']);
        }
        foreach ($this->operators->toSelect() as $k => $v) {
            if ($isAdmin || $k == $login['id']) {
                $operators[$k] = $v;
            }
        }
        // URL สำหรับส่งให้ตาราง
        $uri = self::$request->createUriWithGlobals(WEB_URL.'index.php');
        // ตาราง
        $table = new DataTable(array(
            /* Uri */
            'uri' => $uri,
            /* Model */
            'model' => \Repair\Setup\Model::toDataTable($params),
            /* รายการต่อหน้า */
            'perPage' => $request->cookie('repairSetup_perPage', 30)->toInt(),
            /* เรียงลำดับ */
            'sort' => $request->cookie('repairSetup_sort', 'create_date desc')->toString(),
            /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
            'onRow' => array($this, 'onRow'),
            /* คอลัมน์ที่ไม่ต้องแสดงผล */
            'hideColumns' => array('id'),
            /* คอลัมน์ที่สามารถค้นหาได้ */
            'searchColumns' => array('name', 'phone', 'job_id', 'topic'),
            /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
            'action' => 'index.php/repair/model/setup/action',
            'actionCallback' => 'dataTableActionCallback',
            /* ตัวเลือกด้านบนของตาราง ใช้จำกัดผลลัพท์การ query */
            'filters' => array(
                array(
                    'name' => 'status',
                    'text' => '{LNG_Technical Service status}',
                    'options' => array(-1 => '{LNG_all items}') + $this->statuses->toSelect(),
                    'value' => $params['status'],
                ),
                array(
                    'name' => 'operator_id',
                    'text' => '{LNG_Operator}',
                    'options' => $operators,
                    'value' => $params['operator_id'],
                ),
                
            ),
            /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
            'headers' => array(
                'job_id' => array(
                    'text' => '{LNG_Job No.}',
                ),
                'create_date' => array(
                    'text' => '{LNG_Received date}',
                    'class' => 'center',
                    'sort' => 'create_date',
                ),
                'request_date' => array(
                    'text' => '{LNG_request date}',
                    'class' => 'center',
                    'sort' => 'request_date',
                ),
                'customer_name' => array(
                    'text' => '{LNG_Customer Name}',
                    'sort' => 'customer_name',
                
                ),
                'topic' => array(
                    'text' => '{LNG_types of objective}', 
                ),
               
                'status' => array(
                    'text' => '{LNG_Technical Service status}',
                    'class' => 'center',
                    'sort' => 'status',
                ),
                'operator_id' => array(
                    'text' => '{LNG_Operator}',
                    'class' => 'center',
                ),
            ),
            /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
            'cols' => array(
                'create_date' => array(
                    'class' => 'center',
                ),
                'request_date' => array(
                    'class' => 'center',
                ),
                'status' => array(
                    'class' => 'center',
                ),
            ),
            /* ปุ่มแสดงในแต่ละแถว */
            'buttons' => array(
                'status' => array(
                    'class' => 'icon-list button orange',
                    'id' => ':id',
                    'title' => '{LNG_Technical Service status}',
                ),
                'description' => array(
                    'class' => 'icon-report button purple',
                    'href' => $uri->createBackUri(array('module' => 'repair-detail', 'id' => ':id')),
                    'title' => '{LNG_Technical Service job description}',
                ),
                'printrepair' => array(
                    'class' => 'icon-print button brown notext',
                    'href' =>  $uri->createBackUri(array('module' => 'repair-printrepair', 'id' => ':id')),
                    'target' => '_export',
                    'title' => '{LNG_Print}',
                ),
            ),
        ));
        // สามารถแก้ไขใบรับซ่อมได้
        if ($isAdmin) {
            $table->actions[] = array(
                'id' => 'action',
                'class' => 'ok',
                'text' => '{LNG_With selected}',
                'options' => array(
                    'delete' => '{LNG_Delete}',
                ),
            );
            $table->buttons['edit'] = array(
                'class' => 'icon-edit button green',
                'href' => $uri->createBackUri(array('module' => 'repair-receive', 'id' => ':id')),
                'title' => '{LNG_Edit} {LNG_Technical Service details}',
            ); 
        }
        // save cookie
        setcookie('repairSetup_perPage', $table->perPage, time() + 2592000, '/', HOST, HTTPS, true);
        setcookie('repairSetup_sort', $table->sort, time() + 2592000, '/', HOST, HTTPS, true);
        // คืนค่า HTML
        return $table->render();
    }

    /**
     * จัดรูปแบบการแสดงผลในแต่ละแถว
     *
     * @param array  $item ข้อมูลแถว
     * @param int    $o    ID ของข้อมูล
     * @param object $prop กำหนด properties ของ TR
     *
     * @return array คืนค่า $item กลับไป
     */
    public function onRow($item, $o, $prop)
    {
        $item['create_date'] = Date::format($item['create_date'], 'd M Y  h:i'); 
        $item['request_date'] = Date::format($item['request_date'], 'd M Y h:i');
      //  $item['phone'] = self::showPhone($item['phone']);
        $item['status'] = '<mark class=term style="background-color:'.$this->statuses->getColor($item['status']).'">'.$this->statuses->get($item['status']); //'{LNG_'.$this->statuses->get($item['status']).'}
      //  $item['operator_id'] = $this->operators->get($item['operator_id']);
        return $item;
    }
  
}
