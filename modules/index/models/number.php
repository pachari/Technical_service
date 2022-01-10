<?php
/**
 * @filesource modules/index/models/number.php
 *
 
 */

namespace Index\Number;

use Kotchasan\Database\Sql;
/**
 * คลาสสำหรับจัดการ Running Number
 
 */
class Model extends \Kotchasan\Model
{
    /**
     * คืนค่าข้อมูล running number
     * ตรวจสอบข้อมูลซ้ำด้วย
     * ถ้ายังไม่เคยกำหนดรหัสรูปแบบ จะคืนค่ารหัสแบบสุ่ม
     *
     * @param int    $id         ID สำหรับตรวจสอบข้อมูลซ้ำ
     * @param string $name       ชื่อฟิลด์ที่ต้องการ
     * @param string $table_name ชื่อตาราง สำหรับตรวจสอบข้อมูลซ้ำ
     * @param string $field      ชื่อฟิลด์ สำหรับตรวจสอบข้อมูลซ้ำ
     * @param string $prefix     สำหรับเติมด้านรหัส เช่น XX- จะได้เป็น XX-0001
     *
     * @return string
     */
    public static function get($id, $name, $table_name, $field, $prefix = '')
    {
        // Model
        $model = new static();
        // Database
        $db = $model->db();
        if (empty(self::$cfg->$name)) {
            // สร้างเลขที่แบบสุ่ม
            $result = $prefix.uniqid();
            // ตรวจสอบข้อมูลซ้ำ
            while ($db->first($table_name, array($field, $result))) {
                $result = $prefix.uniqid();
            }
        } else {
                //check date
                $lastdate = self::getlastcreatedate();
                if( intval($lastdate[0]['Lastdate']) ==  intval(date('Ym'))){
                           // number table
                           $table_number = $model->getTableName('number');
                           // ตรวจสอบรายการที่เลือก
                           $number = $db->first($table_number, array('type', $name));

                          // if ($number) {
                               $next_id = 1 + (int) $number->auto_increment;
                         // } else {
                           //    $next_id = 1;
                          // }
                           // ตรวจสอบข้อมูลซ้ำ
                           while (true) {
                               $result = $prefix.\Kotchasan\Number::printf(self::$cfg->$name, $next_id);
                               $search = $db->first($table_name, array($field, $result));
                               if (!$search || ($id > 0 && $search->id == $id)) {
                                   break;
                               } else {
                                   ++$next_id;
                               }
                           }
                }else{
                          // number table
                            $table_number = $model->getTableName('number');
                            // ตรวจสอบรายการที่เลือก
                            $number = $db->first($table_number, array('type', $name));

                         /*   if ($number) {
                                $next_id = 1 + (int) $number->auto_increment;
                            } else {*/
                                $next_id = 1;
                         //   }
                            // ตรวจสอบข้อมูลซ้ำ
                            while (true) {
                                $result = $prefix.\Kotchasan\Number::printf(self::$cfg->$name, $next_id);
                                $search = $db->first($table_name, array($field, $result));
                                if (!$search || ($id > 0 && $search->id == $id)) {
                                    break;
                                } else {
                                    ++$next_id;
                                }
                            }
                           
                }
                 // อัปเดต running number
                 if ($number) {
                    $db->update($table_number, array('type', $name) , array('auto_increment' => $next_id) );
                } else {
                    $db->insert($table_number, array(
                        'type' => $name,
                        'auto_increment' => $next_id,
                        'last_update' => date('Y-m-d'),
                    ));
                }
          
        }
        // คืนค่า
        return $result;
    }
    public static function getlastcreatedate(){
       return \Kotchasan\Model::createQuery()
        ->select(SQL::DATE_FORMAT('create_date', '%Y%m','Lastdate') )
        ->from('repair')
        ->order('id desc')
        ->limit(1)
        ->toArray()
        ->execute();
        
    }
}
