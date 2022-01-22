<?php
/**
 * @filesource modules/repair/modules/operator.php
 *
 
 */

namespace Repair\Operator;

use Gcms\Login;

/**
 * อ่านรายชื่อช่างซ่อมทั้งหมด
 
 */
class Model extends \Kotchasan\KBase
{
    /**
     * @var mixed
     * @param array  $login;
     */
    private $operators;
    private $operators2;
    
    /**
     * Query รายชื่อช่างซ่อม
     *
     * @return array
     */
    public static function all()
    {
        return \Kotchasan\Model::createQuery()
            ->select('id', 'name')
            ->from('user')
            ->where(array(
                array('active', 1),
                array('permission', 'LIKE', '%,can_manage_repair,%'), //can_manage_repair
            ))
            ->order('name')
            ->toArray()
            ->execute();
    }

    /**
     * อ่านรายชื่อช่างซ่อม
     *
     * @return \static
     */
    public static function create()
    {
        $obj = new static();
        $obj->operators = array();
        foreach (self::all() as $item) {
            $obj->operators[$item['id']] = $item['name'];
        }
        return $obj;
    }

    /**
     * อ่านรายชื่อช่างซ่อมสำหรับใส่ลงใน select.
     *
     * @return array
     */
    public function toSelect()
    {
        return $this->operators;
    }

    /* for approve */
    public static function all_approve()
    {

        if(!empty($login = Login::isMember())){
        return \Kotchasan\Model::createQuery()
            ->select('id', 'name')
            ->from('user')
            ->where(array(
                array('active', 1),
                array('id',$login['id']),
                array('permission', 'LIKE', '%,approve_repair,%'),
                
            ))
            ->order('name')
            ->toArray()
            ->execute();
        }
    }

    /**
     * อ่านรายชื่อช่างซ่อม
     *
     * @return \static
     */
    public static function create_approve()
    {

        $obj = new static();
        $obj->operators2 = array();
        foreach (self::all_approve() as $item) {
                $obj->operators2[$item['id']] = $item['name'];
        }
        return $obj;
    }

    /**
     * อ่านรายชื่อช่างซ่อมสำหรับใส่ลงใน select.
     *
     * @return array
     */
    public function toSelect_approve()
    {
        return $this->operators2;
    }

    /**
     * อ่านชื่อช่างที่ $id.
     *
     * @param int $id
     *
     * @return string
     */
    public function get($id)
    {
        return isset($this->operators[$id]) ? $this->operators[$id] : '';
    }

}
