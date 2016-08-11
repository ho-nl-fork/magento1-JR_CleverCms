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

class JR_CleverCms_Block_Adminhtml_Cms_Page_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Main
{
    /**
     * @var Mage_Core_Model_Store
     */
    protected $_store = null;

    protected function _prepareForm()
    {
        $helper = Mage::helper('jr_clevercms/cms_page');

        /* @var $model Mage_Cms_Model_Page */
        $model = Mage::registry('cms_page');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $store = $this->getStore();
        $model = $helper->getPage($model, $store);

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => $helper->__('Page Information')]);

        if ($model && $model->getPageId()) {
            $fieldset->addField('page[main][' . $store->getId() . '][page_id]', 'hidden', [
                'name' => 'page[main][' . $store->getId() . '][page_id]',
                'value'=> $model ? $model->getId() : '',
            ]);
        }

        $useStoreId = $fieldset->addField('page[main][' . $store->getId() . '][use_store_id]', 'select', [
            'name'      => 'page[main][' . $store->getId() . '][use_store_id]',
            'label'     => $helper->__('Use Store View'),
            'title'     => $helper->__('Use Store View'),
            'values'    => Mage::getSingleton('ho_cms/system_store')->getOptions($store->getId()),
            'value'     => $helper->getUseStoreIdValue($model, $store),
        ]);

        $fieldset->addField('page[main][' . $store->getId() . '][title]', 'text', [
            'name'      => 'page[main][' . $store->getId() . '][title]',
            'label'     => $helper->__('Page Title'),
            'title'     => $helper->__('Page Title'),
            'required'  => true,
            'disabled'  => $isElementDisabled,
            'value'     => $model ? $model->getTitle() : '',
        ]);

        $fieldset->addField('page[main][' . $store->getId() . '][identifier]', 'text', [
            'name'      => 'page[main][' . $store->getId() . '][identifier]',
            'label'     => $helper->__('URL Key'),
            'title'     => $helper->__('URL Key'),
            'required'  => true,
            'class'     => 'validate-identifier',
            'note'      => $helper->__('Relative to Website Base URL'),
            'disabled'  => $isElementDisabled,
            'value'     => $model ? $model->getIdentifier() : '',
        ]);

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'disabled'  => $isElementDisabled,
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('cms')->__('Status'),
            'title'     => Mage::helper('cms')->__('Page Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => $model ? $model->getAvailableStatuses() : '',
            'disabled'  => $isElementDisabled,
        ));

        // @todo necessary?
//        if (!$model->getId()) {
//            $model->setData('is_active', $isElementDisabled ? '0' : '1');
//        }

        Mage::dispatchEvent('adminhtml_cms_page_edit_tab_main_prepare_form', array('form' => $form));

        $this->setForm($form);

        return Mage_Adminhtml_Block_Widget_Form::_prepareForm();
    }

    /**
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * @param Mage_Core_Model_Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }
}
