<?php
/**
 * @filesource modules/index/views/registercustomer.php
 *
 
 */

namespace Index\RegisterCustomer;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;
use Kotchasan\DataTable;
use Kotchasan\Form;

/**
 * module=registercustomer
 
 */
class View extends \Gcms\View
{
    /**
     * ลงทะเบียนลูกค้าใหม่
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {

            $form = Html::create('form', array(
                'id' => 'setup_frm',
                'class' => 'setup_frm',
                'autocomplete' => 'off',
                'action' => 'index.php/index/model/registercustomer/submit',
                'onsubmit' => 'doFormSubmit',
                'ajax' => true,
                'token' => true,
            ));
            $fieldset = $form->add('fieldset', array(
                'title' => '{LNG_Details of} {LNG_Customer}',
            ));
            //set row
            $groups = $fieldset->add('groups');
            $groups_customer = $fieldset->add('groups',); 
            $groups_address = $fieldset->add('groups',); 
            // ประเภทลูกค้า
            $groups->add('select', array(
                'id' => 'customer_type',
                'labelClass' => 'g-input icon-category',
                'itemClass' => 'width20',
                'label' => '{LNG_Type}{LNG_Customer}',
                'options' => Language::get('cus_type'),
                //  'value' => $index[0]['customer_type_id'],
            ));
                // Customer code
                $groups_customer->add('text', array(
                'id' => 'customer_code',
                'labelClass' => 'g-input icon-number',
                'itemClass' => 'width15',
                'label' => '{LNG_Cus_code}',
                'maxlength' => 20,
                // 'value' => $index[0]['customer_code'],
            ));
            // Customer name
            $groups_customer->add('text', array(
                'id' => 'customer_name',
                'labelClass' => 'g-input icon-product',
                'itemClass' => 'width50',
                'label' => '{LNG_Customer Name}',
                'maxlength' => 100,
                //  'value' => $index[0]['customer_name'],
            ));
            // address
            $groups_address->add('textarea', array(
                'id' => 'address',
                'labelClass' => 'g-input icon-addressbook',
                'itemClass' => 'width50',
                'label' => '{LNG_Address}', 
                'rows' => 3,
                // 'value' => $index[0]['address'],
            ));
            // contact_id
            $fieldset ->add('hidden', array(
                'id' => 'contact_id',
                //'value' => $index[0]['contact_id'],
            ));

                // ตารางหมวดหมู่
            $table2 = new DataTable(array(
                /* ข้อมูลใส่ลงในตาราง */
                'datas' => \index\Editcustomer\Model::toDataTable(0),
                /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
                'onRow' => array($this, 'onRow'),
                'hideColumns' => array('contact_id'), 
                /* กำหนดให้ input ตัวแรก (id) รับค่าเป็นตัวเลขเท่านั้น */
                // 'onInitRow' => 'initFirstRowNumberOnly',
                'border' => true,
                'responsive' => true,
                'pmButton' => true,
                'showCaption' => false,
                'headers' => array(
                    'contact_name' => array(
                        'text' => '{LNG_Contact_name}',
                    ),
                    'contact_tel' => array(
                        'text' => '{LNG_Contact_tel}',
                    ),), ));   

            $fieldset->add('div', array(
                'class' => 'item',
                'innerHTML' => $table2->render(),
            ));
            $fieldset = $form->add('fieldset', array(
                'class' => 'submit',
            ));
            $fieldset->add('submit', array(
                'class' => 'button save large icon-register',
                'value' => '{LNG_Save}',
            ));
            $fieldset ->add('hidden', array(
                'id' => 'customer_id',
                // 'value' => $index[0]['customer_id'],
            ));
            return $form->render();
    }
    
            /**
             * จัดรูปแบบการแสดงผลในแต่ละแถว
             *
             * @param array  $item ข้อมูลแถว
             * @param int    $o    ID ของข้อมูล
             * @param object $prop กำหนด properties ของ TR
             *
             * @return array
             */
            public function onRow($item, $o, $prop)
            {
                $item['contact_name'] = Form::text(array(
                    'name' => 'contact_name[]',
                    'labelClass' => 'g-input',
                    //  'size' => 2,
                    //  'maxlength' => 128,
                    'value' => $item['contact_name'],
                ))->render();
                $item['contact_tel'] = Form::text(array(
                    'name' => 'contact_tel[]',
                    'labelClass' => 'g-input',
                    'value' => $item['contact_tel'],
                ))->render();
                return $item;
            }
}
