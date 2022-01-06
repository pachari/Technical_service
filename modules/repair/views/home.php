<?php
/**
 * @filesource modules/repair/views/Home.php
 *
 
 */

namespace Repair\Home;

use Kotchasan\Html;

/**
 * module=repair-home
 
 */
class View extends \Gcms\View
{
    /**
     * ตั้งค่าโมดูล
     *
     * @return string
     */
    public static function render($request, $card)
    {
       // สถานะสมาชิก
      $member_status = array(-1 => '{LNG_all items}');
      foreach (self::$cfg->member_status as $key => $value) {
          $member_status[$key] = '{LNG_'.$value.'}';
      }
      //create form search
          $form = Html::create('form', array(
            'id' => 'dash_search_frm',
            'class' => 'setup_frm',
            'autocomplete' => 'off',
            'action' => 'index.php/repair/model/home/submit',
            'onsubmit' => 'doFormSubmit',
            'ajax' => true,
            'token' => true,
          ));
          $fieldset = $form->add('fieldset', array(
            
             // 'title' => '{LNG_Search}',
             //'Class' => 'item2',
          ));
          
          // Date Start create job
          $fieldset->add('date', array(                 
              'id' => 'dash_first_Date',
              'labelClass' => 'g-input',
              'itemClass' => 'item2',
              'label' => '{LNG_Received date} ',
            
          ));
          // Date End job
          $fieldset->add('date', array(                 
              'id' => 'dash_end_Date',
              'labelClass' => 'g-input',
              'itemClass' => 'item',
              'label' => '{LNG_to} ',
        ));
          // repair_first_status
          $fieldset->add('select', array(
              'id' => 'dash_Member',
              'labelClass' => 'g-input icon-tools',
              'itemClass' => 'item',
              'label' => '{LNG_Member}',
              'options' => $member_status,
              'value' => isset(self::$cfg->repair_first_status) ? self::$cfg->repair_first_status : 1,

          ));
      /*    // repair_first_status
          $fieldset->add('hide', array(
            'id' => '_login',
            'value' =>$login,

        ));*/
          // repair_job_no
         /* $fieldset->add('text', array(
              'id' => 'repair_job_no',
              //'labelClass' => 'g-input icon-number',
              //'itemClass' => 'item',
              'label' => '{LNG_Job No.}',
              'placeholder' => '{LNG_number format such as %04d (%04d means the number on 4 digits, up to 11 digits)}',
              'comment' => '{LNG_Leave empty for generate auto}',
              'value' => isset(self::$cfg->repair_job_no) ? self::$cfg->repair_job_no : 'job%04d',
          ));*/
          
          $fieldset = $form->add('fieldset', array(
              'class' => 'submit right',
          ));
          // submit
          $fieldset->add('submit', array(
              'class' => 'button go',
              'value' => ' {LNG_Search}',
          ));
         //button search
         
          // คืนค่า HTML
          $card->set(\Kotchasan\Password::uniqid(),$form->render()); 
    }
   
}
