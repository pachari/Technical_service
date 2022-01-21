<?php
/**
 * @filesource modules/index/views/editprofile.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Editprofile;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;


/**
 * module=editprofile
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * ฟอร์มแก้ไขสมาชิก
     *
     * @param Request $request
     * @param array   $user
     * @param array   $login
     *
     * @return string
     */
    public function render(Request $request, $user, $login)
    {
        // แอดมิน
        $login_admin = Login::isAdmin();
        // form
        $form = Html::create('form', array(
            'id' => 'setup_frm',
            'class' => 'setup_frm',
            'autocomplete' => 'off',
            'action' => 'index.php/index/model/editprofile/submit',
            'onsubmit' => 'doFormSubmit',
            'ajax' => true,
            'token' => true,
        ));
        if ($user['active'] == 1) {
            $fieldset = $form->add('fieldset', array(
                'title' => '{LNG_Login information}',
            ));
            $groups = $fieldset->add('groups');
            // username
            $groups->add('text', array(
                'id' => 'register_username',
                'itemClass' => 'width50',
                'labelClass' => 'g-input icon-email',
                'label' => '{LNG_Email}',
                'comment' => '{LNG_Email address used for login or request a new password}',
                'disabled' => $login_admin ? false : true,
                'maxlength' => 50,
                'value' => $user['username'],
                'validator' => array('keyup,change', 'checkUsername', 'index.php/index/model/checker/username'),
            ));
            // password, repassword
            $groups = $fieldset->add('groups', array(
                'comment' => '{LNG_To change your password, enter your password to match the two inputs}',
            ));
            // password
            $groups->add('password', array(
                'id' => 'register_password',
                'itemClass' => 'width50',
                'labelClass' => 'g-input icon-password',
                'label' => '{LNG_Password}',
                'placeholder' => '{LNG_Passwords must be at least four characters}',
                'maxlength' => 20,
                'validator' => array('keyup,change', 'checkPassword'),
            ));
            // repassword
            $groups->add('password', array(
                'id' => 'register_repassword',
                'itemClass' => 'width50',
                'labelClass' => 'g-input icon-password',
                'label' => '{LNG_Repassword}',
                'placeholder' => '{LNG_Enter your password again}',
                'maxlength' => 20,
                'validator' => array('keyup,change', 'checkPassword'),
            ));
        }
        $fieldset = $form->add('fieldset', array(
            'title' => '{LNG_Details of} {LNG_User}',
        ));
        $groups = $fieldset->add('groups');
        // name
        $groups->add('text', array(
            'id' => 'register_name',
            'labelClass' => 'g-input icon-customer',
            'itemClass' => 'width50',
            'label' => '{LNG_Name}',
            'maxlength' => 100,
            'value' => $user['name'],
        ));
        // sex
        $groups->add('select', array(
            'id' => 'register_sex',
            'labelClass' => 'g-input icon-sex',
            'itemClass' => 'width50',
            'label' => '{LNG_Sex}',
            'options' => Language::get('SEXES'),
            'value' => $user['sex'],
        ));
        // หมวดหมู่
        $category = \Index\Category\Model::init();
        $a = 0;
        foreach (Language::get('CATEGORIES', array()) as $k => $label) {
            if ($a % 2 == 0) {
                $groups = $fieldset->add('groups');
            }
            $a++;
            $groups->add('text', array(
                'id' => $k,
                'labelClass' => 'g-input icon-menus',
                'itemClass' => 'width50',
                'label' => $label,
                'datalist' => $category->toSelect($k),
                'value' => isset($user[$k]) ? $user[$k] : '',
                'text' => '',
            ));
        }
        $groups = $fieldset->add('groups');
        // phone
        $groups->add('text', array(
            'id' => 'register_phone',
            'labelClass' => 'g-input icon-phone',
            'itemClass' => 'width50',
            'label' => '{LNG_Phone}',
            'maxlength' => 32,
            'value' => $user['phone'],
        ));
        // id_card
        $groups->add('text', array(//number
            'id' => 'register_id_card',
            'labelClass' => 'g-input icon-subcategory',
            'itemClass' => 'width50',
            'label' => '{LNG_position}',
            'maxlength' => 100,
            'value' => $user['id_card'],
           // 'validator' => array('keyup,change', 'checkIdcard'),
        ));
        // picture E-signature
        
        $img = is_file(ROOT_PATH.DATA_FOLDER.'E-signature/'.'Esig_'.$user['id'].'.jpg') ? WEB_URL.DATA_FOLDER.'E-signature/'.'Esig_'.$user['id'].'.jpg' : WEB_URL.'modules/inventory/img/noimage.png';
        $fieldset->add('file', array(
        'id' => 'Esignature',
        'labelClass' => 'g-input icon-upload',
        'itemClass' => 'item',
        'label' => '{LNG_signature approve}',
        'comment' => Language::replace('Browse image uploaded, type :type', array(':type' => 'jpg, jpeg, png')).' ({LNG_resized automatically})',
        'dataPreview' => 'imgPicture',
       // 'previewSrc' => $img,
        'previewSrc_disable' => $img,
        'accept' => array('jpg', 'jpeg', 'png'), 
        ));  

        // address
        $fieldset->add('hidden', array( //text
            'id' => 'register_address',
          /*  'labelClass' => 'g-input icon-address',
            'itemClass' => 'item',
            'label' => '{LNG_Address}',
            'maxlength' => 150,*/
            'value' => $user['address'],
        ));
        $groups = $fieldset->add('groups');
        // country
        $groups->add('hidden', array( //text
            'id' => 'register_country',
           /* 'labelClass' => 'g-input icon-world',
            'itemClass' => 'width33',
            'label' => '{LNG_Country}',
            'datalist' => \Kotchasan\Country::all(), */
            'value' => $user['country'],
        ));
        // provinceID
        $groups->add('hidden', array( //text
            'id' => 'register_province',
           /* 'name' => 'register_provinceID',
            'labelClass' => 'g-input icon-location',
            'itemClass' => 'width33',
            'label' => '{LNG_Province}',
            'datalist' => array(),
            'text' => $user['province'],*/
            'value' => $user['provinceID'],
        ));
        // zipcode
        $groups->add('hidden', array( //number
            'id' => 'register_zipcode',
           /* 'labelClass' => 'g-input icon-number',
            'itemClass' => 'width33',
            'label' => '{LNG_Zipcode}',
            'maxlength' => 10, */
            'value' => $user['zipcode'],
        ));
        
        //ดึงข้อมูล User มาแสดงให้เลือก
        $user_tc = array();
        $this->user_tc   = \index\Report\Model::create(); 
        $this->user_id_tc   = \index\Report\Model::all();
        if ($login_admin) {
                $fieldset = $form->add('fieldset', array(
                    'title' => '{LNG_Other}',
                ));
                //แสดงรายชื่อที่จะเลือกเป็นหัวหน้า
                foreach ($this->user_tc->toselect() as $k => $v) { 
                    if ($login_admin) {
                            if ($k== $user['head']) { 
                                $user_tc[0] = $v ; 
                            }
                        $user_tc[$k] = $v;
                    }
                }
             // user_id
             $fieldset->add('select', array(
                'id' => 'user_id',
                'itemClass' => 'item',
                'label' => '{LNG_Approve}',
                'labelClass' => 'g-input icon-star0',
                //'disabled' => $user['id']  == $login_admin['head'] ? true : false,
                'value' => $user['head'], 
                'options' =>  $this->user_tc->toselect(),//$user_tc, 
                //'options' =>  $user_tc, 
                //'value' => $user_tc['id'],
            ));
            // status
            $fieldset->add('select', array(
                'id' => 'register_status',
                'itemClass' => 'item',
                'label' => '{LNG_Member status}',
                'labelClass' => 'g-input icon-star0',
                'disabled' => $login_admin['id'] == $user['id'] ? true : false,
                'options' => self::$cfg->member_status,
                'value' => $user['status'],
            ));
            // permission
            $fieldset->add('checkboxgroups', array(
                'id' => 'register_permission',
                'itemClass' => 'item',
                'label' => '{LNG_Permission}',
                'labelClass' => 'g-input icon-list',
                'options' => \Gcms\Controller::getPermissions(),
                'value' => $user['permission'],
            ));
        }
        $fieldset = $form->add('fieldset', array(
            'class' => 'submit',
        ));
        // submit
        $fieldset->add('submit', array(
            'class' => 'button save large icon-save',
            'value' => '{LNG_Save}',
        ));
        $fieldset->add('hidden', array(
            'id' => 'register_id',
            'value' => $user['id'],
        ));
        // Javascript
        $form->script('initEditProfile("register");');
        // คืนค่า HTML
        
        return $form->render();
        
    }
}
