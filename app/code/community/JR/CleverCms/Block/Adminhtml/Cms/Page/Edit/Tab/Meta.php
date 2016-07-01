<?php
/**
 * JR_CleverCms
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the H&O Commercial License
 * that is bundled with this package in the file LICENSE_HO.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.h-o.nl/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@h-o.com so we can send you a copy immediately.
 *
 * @category    JR
 * @package     JR_CleverCms
 * @copyright   Copyright © 2016 H&O (http://www.h-o.nl/)
 * @license     H&O Commercial License (http://www.h-o.nl/license)
 * @author      Maikel Koek – H&O <info@h-o.nl>
 */

class JR_CleverCms_Block_Adminhtml_Cms_Page_Edit_Tab_Meta
    extends Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Meta
{
    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $model = Mage::registry('cms_page');

        $fieldset = $form->addFieldset('meta_fieldset', array('legend' => Mage::helper('cms')->__('Meta Data'), 'class' => 'fieldset-wide'));

        $fieldset->addField('meta_keywords', 'textarea', array(
            'name' => 'meta_keywords',
            'label' => Mage::helper('cms')->__('Keywords'),
            'title' => Mage::helper('cms')->__('Meta Keywords'),
            'disabled'  => $isElementDisabled
        ));

        $fieldset->addField('meta_description', 'textarea', array(
            'name' => 'meta_description',
            'label' => Mage::helper('cms')->__('Description'),
            'title' => Mage::helper('cms')->__('Meta Description'),
            'disabled'  => $isElementDisabled
        ));

        Mage::dispatchEvent('adminhtml_cms_page_edit_tab_meta_prepare_form', array('form' => $form));

        $form->setValues($model->getData());

        $this->setForm($form);

        return Mage_Adminhtml_Block_Widget_Form::_prepareForm();
    }
}
