<?php
/**
 * @filesource modules/inventory/views/settings.php
 *
 
 */

namespace Inventory\Settings;

use Kotchasan\Html;

/**
 * module=inventory-settings
 
 */
class View extends \Gcms\View
{
    /**
     * ตั้งค่าโมดูล
     *
     * @return string
     */
    public function render()
    {
        // form
        $form = Html::create('form', array(
            'id' => 'setup_frm',
            'class' => 'setup_frm',
            'autocomplete' => 'off',
            'action' => 'index.php/inventory/model/settings/submit',
            'onsubmit' => 'doFormSubmit',
            'ajax' => true,
            'token' => true,
        ));
        $fieldset = $form->add('fieldset', array(
            'title' => '{LNG_Size of} {LNG_Image}',
        ));
        // inventory_w
        $fieldset->add('text', array(
            'id' => 'inventory_w',
            'labelClass' => 'g-input icon-width',
            'itemClass' => 'item',
            'label' => '{LNG_Width}',
            'comment' => '{LNG_Image size is in pixels} {LNG_Uploaded images are resized automatically}',
            'value' => isset(self::$cfg->inventory_w) ? self::$cfg->inventory_w : 500,
        ));
        $fieldset = $form->add('fieldset', array(
            'class' => 'submit',
        ));
        // submit
        $fieldset->add('submit', array(
            'class' => 'button save large icon-save',
            'value' => '{LNG_Save}',
        ));
        // คืนค่า HTML
        return $form->render();
    }
}
