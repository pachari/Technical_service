<?php
/**
 * @filesource modules/repair/views/detail.php
 *
 
 */

namespace Repair\Detail;

use Gcms\Login;
use Kotchasan\DataTable2;
use Kotchasan\Date;
use Kotchasan\Template;

/**
 * module=repair-detail
 
 */
class View extends \Gcms\View
{
    /**
     * @var mixed
     */
    private $statuses;

    /**
     * แสดงรายละเอียดการซ่อม
     *
     * @param object $index
     * @param array  $login
     *
     * @return string
     */
    public function render($index, $login)
    {
        // สถานะการซ่อม
        $this->statuses = \Repair\Status\Model::create();
        // อ่านสถานะการทำรายการทั้งหมด
        $statuses = \Repair\Detail\Model::getAllStatus($index->id);
        // URL สำหรับส่งให้ตาราง
        $uri = self::$request->createUriWithGlobals(WEB_URL.'index.php');      
        //เช็คกลุ่มผู้ใช้งาน
        $gmember = \Index\Member\Model::getMemberstatus($index->s_group);
        if($index->status == '9' || $index->status == '10' || $index->status == '11' || $index->status == '12'){ // template for approve/none approve
            if (Login::checkPermission($login, array('can_manage_technical','can_repair','approve_repair')) ){ //can_manage_repair
                $template = Template::createFromFile(ROOT_PATH.'modules/repair/views/detail2.html');
            }else{  $template = Template::createFromFile(ROOT_PATH.'modules/repair/views/detail3.html');  } 
        }else{ // template standard All Status
            $template = Template::createFromFile(ROOT_PATH.'modules/repair/views/detail.html');   }
            /*เอารูปภาพE-sig approve มาแสดง  */
            $img = is_file(ROOT_PATH.DATA_FOLDER.'approve/'.'R'.$index->id.'-'.Date::format($index->date_approve, 'md').'.jpg') ? WEB_URL.DATA_FOLDER.'approve/'.'R'.$index->id.'-'.Date::format($index->date_approve, 'md').'.jpg' : WEB_URL.'modules/inventory/img/noimage.png';
            /*อาจากการอัพโหลดรูปลายเซ็นต์*/
            //$img    = is_file(ROOT_PATH.DATA_FOLDER.'E-signature/'.'Esig_'.$index->send_approve.'.jpg') ? WEB_URL.DATA_FOLDER.'E-signature/'.'Esig_'.$index->send_approve.'.jpg' : WEB_URL.'modules/inventory/img/noesig.png';
           //หาประเภทการแจ้งขอบริการ
            foreach(self::$cfg->type_job_number as $k => $value){
                if($index->type_job_number == $k){
                    $type_job_run = $value;
                }
            }
              /*เอารูปภาพแนบเปิดงานมาแสดง */
           $html_img = ' <tr><td class="icon-iconview">{LNG_Image}</td>';
           for($i=1;$i<=$index->attachment_no;$i++){   
                $img2[$i] = is_file(ROOT_PATH.DATA_FOLDER.'file_attachment_user/'. $type_job_run.'_'.$index->id.'_'.$i.'.jpg') ? WEB_URL.DATA_FOLDER.'file_attachment_user/'.$type_job_run.'_'.$index->id.'_'.$i.'.jpg' : WEB_URL.'modules/inventory/img/noesig.png';   
                $html_img .= '<td><img class="resize" src='.$img2[$i].'></td>';        
            } $html_img .= '</tr>';
            
        // ตาราง
        $table = new DataTable2(array(
            /* Uri */
            'uri' => $uri,
            /* array datas */
            'datas' => $statuses,
            /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
            'onRow' => array($this, 'onRow'),
            /* คอลัมน์ที่ไม่ต้องแสดงผล */
            'hideColumns' => array('id'),
            /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
            'action' => 'index.php/repair/model/detail/action?repair_id='.$index->id,
            'actionCallback' => 'dataTableActionCallback',
            /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
            'headers' => array(
                'name' => array(
                    'text' => '{LNG_Operator}',
                    'width' => '200px'
                ),
                'status' => array(
                    'text' => '{LNG_Technical Service status}',
                    'class' => 'center',
                ),
                'create_date' => array(
                    'text' => '{LNG_Transaction date}', 
                    'class' => 'center',
                ),
                'comment' => array(
                    'text' => '{LNG_Comment}',
                ),
                'attachment' => array(
                    'text' => '{LNG_file_attachment}',
                ),
                'picture' => array(
                    'text' => '{LNG_Image}',
                ),
                
            ),
            /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
            'cols' => array(
                'status' => array(
                    'class' => 'center',
                ),
                'create_date' => array(
                    'class' => 'center',
                ),
            ),
        ));
        $login = Login::isMember();
        if (Login::checkPermission($login, array('can_config'))) { //array('can_manage_repair', 'can_repair')
            /* ปุ่มแสดงในแต่ละแถว */
            $table->buttons = array(
            /*    'file_attachment' => array(
                    'class' => 'button purple notext notext icon-download',
                    'id' => ':id',
                    'title' => '{LNG_File}',             
                ),   */
                'delete' => array(
                    'class' => 'icon-delete button red notext',
                    'id' => ':id',
                    'title' => '{LNG_Delete}',
                ),
            );
        }
          //  print_r( $tablepic);
        $template->add(array(
            '/%NAME%/' => $index->name,
            '/%PHONE%/' => $index->phone, 
            '/%TOPIC%/' => $index->topic,
            '/%PRODUCT_NO%/' => $index->product_no,
            '/%CUSTOMER%/' => $index->customer_name,
            '/%ADDRESS%/' => $index->address,
            '/%CONTACT%/' => $index->contact_name.' '.$index->contact_tel.' '.$index->position,
            '/%JOB_DESCRIPTION%/' => nl2br($index->job_description),
            '/%CREATE_DATE%/' => Date::format($index->create_date, 'd M Y H:i'),
            '/%REQUEST_DATE%/' => Date::format($index->request_date, 'd M Y H:i'),
            '/%COMMENT%/' => $index->comment,
            '/%DETAILS%/' => $table->render(),
            '/%NAMEAPPROVE%/' => $index->send_approve2,
            '/%JOB%/' => $index->job_id,
            '/%GROUP%/' => $gmember,
            '/%ESIG%/' => $img,
            '/%DATE_APPROVE%/' => Date::format($index->date_approve, 'd M Y H:i'),
            '/%COST%/' => $index->COST,
            //'/%UPIC%/' => $img2,
            '/%UPIC%/' =>$html_img,
           // '/%FILE_ATTACHMENT%/' => $file_attachment,
        ));
        // คืนค่า HTML
        return $template->render();
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
        $item['comment'] = nl2br($item['comment']);
        $item['create_date'] = Date::format($item['create_date'], 'd M Y H:i');
        $item['status'] = '<mark class=term style="background-color:'.$this->statuses->getColor($item['status']).'">'.$this->statuses->get($item['status']).'</mark>';

        return $item;
    }
}
