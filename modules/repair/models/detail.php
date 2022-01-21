<?php
/**
 * @filesource modules/repair/models/detail.php
 *
 
 */

namespace Repair\Detail;

use Gcms\Login;
use Kotchasan\Date;
//use Kotchasan\File;
use Kotchasan\Database\Sql;
use Kotchasan\Http\Request;
use Kotchasan\Language;


/**
 * module=repair-detail
 
 */
class Model extends \Kotchasan\Model
{
    /**
     * อ่านรายละเอียดการทำรายการจาก $id
     *
     * @param int $id
     *
     * @return object
     */
    public static function get($id)
    {
        $q0_name = static::createQuery()
            ->select('U1.name as send_approve2')
            ->from('user U1')
            ->where(array('U1.id', 'R.send_approve'));
        $q0_group = static::createQuery()    
            ->select('U3.status as s_group')
            ->from('user U3')
            ->where(array('U3.id', 'R.create_by'));
        $q1 = static::createQuery()
            ->select('repair_id', Sql::MAX('id', 'max_id'))
            ->from('repair_status')
            ->groupBy('repair_id');
        $sql = static::createQuery()
            ->select('R.*', 'U.name', 'U.phone', 'V.topic', 'S.create_date as date_approve','S.status', 'S.comment', 'S.operator_id', 'S.id status_id',array( $q0_name,'send_approve2'),array( $q0_group,'s_group'),SQL::SUM('S.cost','COST'),'C.address','C.customer_name','C.customer_code','CT.contact_name','CT.contact_tel','CT.position' )
            ->from('repair R')
            ->join(array($q1, 'T'), 'LEFT', array('T.repair_id', 'R.id'))
            ->join('repair_status S', 'LEFT', array('S.id', 'T.max_id'))
            ->join('inventory_items I', 'LEFT', array('I.product_no', 'R.product_no'))
            ->join('inventory V', 'LEFT', array('V.id', 'I.inventory_id'))
            ->join('user U', 'LEFT', array('U.id', 'R.create_by')) //customer_id
            ->join('customer C', 'LEFT', array('C.id', 'R.customer_id'))
            ->join('contact CT', 'LEFT', array('CT.id', 'R.contact_id'))
            
            ->where(array('R.id', $id))
            ->order('S.id DESC');
        return  static::createQuery()
            ->from(array($sql, 'Q'))
            ->groupBy('Q.id')
            ->first();

      

    }

    /**
     * อ่านสถานะการทำรายการทั้งหมด
     *
     * @param int $id
     *
     * @return array
     */
    public static function getAllStatus($id)
    {
        return static::createQuery()
            ->select('S.id', 'U.name', 'S.status', 'S.create_date', 'S.comment','S.attachment') 
            ->from('repair_status S')
            ->join('user U', 'LEFT', array('U.id', 'S.operator_id'))
            ->where(array('S.repair_id', $id))
            ->order('S.id')
            ->toArray()
            ->execute();
    }

    /**
     * รับค่าจาก action (detail.php)
     *
     * @param Request $request
     */
    public function action(Request $request)
    {
        $ret = array();
        // session, referer, member, ไม่ใช่สมาชิกตัวอย่าง
        if ($request->initSession() && $request->isReferer() && $login = Login::isMember()) {
           
            if (Login::notDemoMode($login)) {  
                // รับค่าจากการ POST
                $action = $request->post('action')->toString();
                $id = $request->post('id')->toString();

                 // id ที่ส่งมา
                 if (preg_match('/^delete_([0-9a-z]+)$/', $id, $match)) {
                    if (isset($_SESSION[$match[1]])) {
                        $file = $_SESSION[$match[1]];
                        if (is_file($file['file'])) {
                            unlink($file['file']);
                        }
                        // คืนค่ารายการที่ลบ
                        $ret['remove'] = 'item_' . $match[1];
                    }
                } elseif (preg_match_all('/,?([0-9]+),?/', $id, $match)) {
                    if ($action === 'delete' && Login::checkPermission($login, array('can_config'))) { //array('can_manage_repair', 'can_repair')
                        // ลบรายละเอียดซ่อม
                        $this->db()->delete($this->getTableName('repair_status'), array('id', (int) $match[1][0]));
                        // reload
                        $ret['location'] = 'reload';
                    }
                }
               if ($action === 'file_attachment') {
                 
                    // อ่านรายการที่เลือก
                    $result = (\Repair\Detail\Model::getFilename($id)) ;
                        if ($result) { 
                            $file = ROOT_PATH . DATA_FOLDER . 'file_attachment/' . $result->attachment;
                            if (is_file($file)) {
                                // id สำหรับไฟล์ดาวน์โหลด
                                $id = md5(uniqid());
                                // บันทึกรายละเอียดการดาวน์โหลดลง SESSION
                                $_SESSION[$id] = array(
                                    'file' => $file,
                                    'name' => $result->attachment,//$result->topic . '.' . $result->ext,
                                    'mime' => '.pdf',//\Kotchasan\Mime::get($result->ext),
                                    //'size' => $result->size,
                                );
                                // คืนค่า
                                $ret['location'] = WEB_URL . 'modules/repair/filedownload.php?id=' . $id;
                            } else {
                                // ไม่พบไฟล์
                            
                                $ret['alert'] = Language::get('Sorry, Item not found It&#39;s may be deleted');
                            }
                    }                       
                }
            }
        }
        if (empty($ret)) {
            
            $ret['alert'] = Language::get('Unable to complete the transaction');
        }
        // คืนค่า JSON
        echo json_encode($ret);
    }

    public static function getFilename($id)
    {
        
        return static::createQuery()
        ->select('attachment')
        ->from('repair_status S')
        //->where(array('S.repair_id', $repair_id))
        ->andwhere(array('S.id', $id))
        ->first();
    }

}