<?php
/**
 * @filesource modules/repair/modules/status.php
 *
 
 */

namespace Repair\Status;

use Gcms\Login;

/**
 * อ่านค่าสถานะของการซ่อม
 
 */
class Model extends \Kotchasan\KBase
{
    /**
     * @var mixed
     */
    private $statuses;
    private $statuses2;
     /**
     * @var mixed
     */
    private $colors;

    /**
     * Query รายการสถานะทั้งหมดที่สามารถเผยแพร่ได้.
     *
     * @return array
     */

    public static function all()
    {
        return \Kotchasan\Model::createQuery()
            ->select('category_id', 'topic', 'color')
            ->from('category')
            ->where(array(
                array('type', 'repairstatus'),
                array('published', 1),
            ))
            ->order('category_id')
            ->toArray()
            ->execute();
    }

    /**
     * อ่านค่าสถานะ
     *
     * @return \static
     */
    public static function create()
    {
            $obj = new static();
            $obj->statuses = array();
            $obj->colors = array();
            foreach (self::all() as $item) {
                $obj->statuses[$item['category_id']] = '{LNG_'.$item['topic'].'}';
                $obj->colors[$item['category_id']] = $item['color'];
            }
           return $obj;
    }
    //อ่าค่าสถานะ Approve

    public static function status_approve()
    {
        return \Kotchasan\Model::createQuery()
            ->select('category_id', 'topic', 'color')
            ->from('category')
            ->where(array(
                array('type', 'repairstatus'), 
                array('published', 1)
            ))
            //->andwhere(array('category_id', 'IN', array(8 ,9,10,11, 12))) //(8 ,9, 10)
            ->order('category_id')
            ->toArray()
            ->execute();
    }

    public static function create_status_approve()
    {
            $obj = new static();
            $obj->statuses = array();
            $obj->colors = array();
            foreach (self::status_approve() as $item) {
                $obj->statuses[$item['category_id']] = '{LNG_'.$item['topic'].'}';
                $obj->colors[$item['category_id']] = $item['color'];
            }
           return $obj;
    }

    /*public static function get_max_status_id()
    {
        return \Kotchasan\Model::createQuery()
            ->select(MAX('id', 'max_id'))
            ->from('repair_status')
            ->toArray()
            ->execute();
    } */

    /**
     * อ่านค่าสีที่ $id
     *
     * @param int $id
     *
     * @return string
     */
    public function getColor($id)
    {
        return isset($this->colors[$id]) ? $this->colors[$id] : 'inherit';
    }

    /**
     * อ่านสถานะที่ $id.
     *
     * @param int $id
     *
     * @return string
     */
    public function get($id)
    {
        return isset($this->statuses[$id]) ? $this->statuses[$id] : 'Unknow';
    }

    /**
     * คืนค่าสถานะการซ่อมสำหรับใส่ลงใน select
     *
     * @return array
     */
    public function toSelect()
    {
        return $this->statuses;
    }

    public function toSelect_status_approve()
    {
        return $this->statuses2;
    }
   
    //อ่าค่าสถานะ Approve
    public static function create_approve()
    {
            $obj = new static();
            $obj->statuses2 = array();
            $obj->colors = array();
            foreach (self::all_approve() as $item) {
                $obj->statuses2[$item['category_id']] = '{LNG_'.$item['topic'].'}';
                $obj->colors[$item['category_id']] = $item['color'];
            }
           return $obj;
    }

    public static function all_approve()
    {
        return \Kotchasan\Model::createQuery()
            ->select('category_id', 'topic', 'color')
            ->from('category')
            ->where(array(
                array('type', 'repairstatus'),
                array('published', 1),
            ))
            ->andwhere(array('category_id', 'IN', array(11, 12))) //(8 ,9, 10)
            ->order('category_id')
            ->toArray()
            ->execute();
    }
}
