<?php
/**
 * @filesource modules/repair/views/receive.php
 *
 *
 *
 * @see http://www.kotchasan.com/
 */

namespace Repair\Receive;
use Kotchasan\Html;
use Kotchasan\Language;
use Gcms\Login;
use Kotchasan\Http\Request;
use Kotchasan\Http\UploadedFile;
/**
 * module=repair-receive
 *
 * 
 *
 * 
 */
class View extends \Gcms\View
{
    /**
     * เพิ่ม-แก้ไข แจ้งงาน
     *
     * @param object $index
     *
     * @return string
     */
    public function render( $request,$index)
    {
        $form = Html::create('form', array(
            'id' => 'setup_frm',
            'class' => 'setup_frm',
            'autocomplete' => 'off',
            'action' => 'index.php/repair/model/receive/submit',
            'onsubmit' => 'doFormSubmit',
            'ajax' => true,
            'token' => true,
        ));
        $fieldset = $form->add('fieldset', array('title' => '{LNG_Technical Service job description}', ));  
        $groups = $fieldset->add('groups',); //, array('comment' => '{LNG_Find equipment by}  {LNG_types of objective}' ) array('urgency' => '{LNG_Find equipment by}  {LNG_types of objective}', )
        $groups_address = $fieldset->add('groups',); 
        $groups_contact = $fieldset->add('groups',); 
        $type_work_name = \repair\Receive\Model::createProduct();
   
        /*-------------------------------------------St moomai----------------------------------------*/
                // date_request
                $groups->add('date', array(
                    'id' => 'date_request',
                    'labelClass' => 'g-input icon-event',
                    'itemClass' => 'width15',
                    'label' => '*{LNG_request date}', 
                    'value' => isset($index->start_date) ? $index->start_date : date('Y-m-d'),
                   // 'disabled' => !$canEdit,
                ));
                // time_request
                $groups->add('time', array(
                    'id' => 'time_request',
                    'labelClass' => 'g-input icon-clock',
                    'itemClass' => 'width15',
                    'label' => '*{LNG_request time}', 
                    'value' => isset($index->start_date) ? $index->start_date : date('H:i'),
                ));
                  // customer_id
                  $fieldset ->add('hidden', array(
                    'id' => 'customer_id',
                    'value' => $index->customer_id,
                ));
                // customer_name
                $groups->add('text', array(
                    'id' => 'customer_name',
                    'labelClass' => 'g-input icon-product',
                    'itemClass' => 'width50',
                    'label' => '*{LNG_Customer Name}',
                    'maxlength' => 20,
                    'value' => $index->customer_name,
                ));
                // address
                $groups_address->add('textarea', array(
                    'id' => 'address',
                    'labelClass' => 'g-input icon-addressbook',
                    'itemClass' => 'width50',
                    'label' => '{LNG_Address}', 
                    'rows' => 1,
                    'disabled' => true,
                ));
                // contact_id
                $fieldset ->add('hidden', array(
                    'id' => 'contact_id',
                    'value' => $index->contact_id,
                ));
                // contact name
                $groups_contact->add('text', array(
                    'id' => 'contact_name',
                    'labelClass' => 'g-input icon-address',
                    'itemClass' => 'width50',
                    'label' => '{LNG_Contact_name}', 
                    'maxlength' => 20,
                ));
                 // contact tel
                 $groups_contact->add('text', array(
                    'id' => 'contact_tel',
                    'labelClass' => 'g-input icon-contract',
                    'itemClass' => 'width50',
                    'label' => '{LNG_Contact_tel}', 
                ));
                // type_job_number
                $fieldset->add('radiogroups', array(
                    'id' => 'type_job_number',
                    'labelClass' => 'g-input icon-list',
                    'itemClass' => 'item',
                    'label' => '*{LNG_type_repair}', 
                    'multiline' => false,
                    'scroll' => false,
                    'options' => self::$cfg->type_job_number, 
                ));
                 // type_work
                 $fieldset->add('radiogroups', array(
                    'id' => 'product_no',
                    'labelClass' => 'g-input icon-list',
                    'itemClass' => 'item',
                    'label' => '*{LNG_types of objective}', 
                    'multiline' => false,
                    'scroll' => false,
                    'options' => $type_work_name->product_no, 
                ));
                // job_description
                $fieldset->add('textarea', array(
                    'id' => 'job_description',
                    'labelClass' => 'g-input icon-file',
                    'itemClass' => 'item',
                    'label' => '{LNG_Detail}',
                    'rows' => 2,
                    'maxlength' => 500,
                    'value' => $index->job_description,
                ));    
                //User upload file Attachment
                    $fieldset->add('file', array(
                        'name' => 'file_attachment_user[]',
                        'id' => 'file_attachment_user',
                        'labelClass' => 'g-input icon-gallery',
                        'itemClass' => 'item',
                        'label' => '{LNG_file_attachment}',
                       // 'comment' => Language::replace('Browse image uploaded, type :type', array(':type' => 'jpg, jpeg, png')).' ({LNG_resized automatically})',
                       'comment' => Language::replace('Upload :type files no larger than :size', array(':type' => 'jpg, jpeg, gif, png', ':size' => UploadedFile::getUploadSize())),  //, pdf
                        'dataPreview' => 'multi_preview',
                        'multiple' => true,
                        'accept' => array('jpg', 'jpeg', 'png'), 
                    // 'previewSrc' => $img,
                    //'previewSrc_disable' => $img,
                        ));      
                // Approve_id
                $fieldset ->add('hidden', array(
                    'id' => 'approve_id',
                    'value' => $index->approve_id,
                ));
                // List Name Approve 
                $fieldset ->add('text', array(
                    'id' => 'approve_name',
                    'labelClass' => 'g-input icon-user',
                    'itemClass' => 'item',
                    'label' => '*{LNG_Approve}',
                    'maxlength' => 20,
                    'value' => $index->approve_name,
                    
                ));

                if ($index->id == 0) {
                        // comment Level of Urgency
                        $fieldset->add('radiogroups', array(
                                'id' => 'urgency',
                            ));
                        // status_id
                        $fieldset->add('hidden', array(
                            'id' => 'status_id',
                            'value' => $index->status_id,
                        ));
                }
                $fieldset = $form->add('fieldset', array(
                    'class' => 'submit',
                ));
                // submit
                $fieldset->add('submit', array(
                    'id' => 'save',
                    'class' => 'button save large icon-save',
                    'value' => '{LNG_Save}',
                ));
                // id
                $fieldset->add('hidden', array(
                    'id' => 'id',
                    'value' => $index->id,
                ));
                // Javascript
                $form->script('initRepairGet();');
                // คืนค่า HTML
                return $form->render();
    }
}
