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
    /**
     * @var Mage_Core_Model_Store
     */
    protected $_store = null;

    protected function _prepareForm()
    {
        $helper = Mage::helper('jr_clevercms/cms_page');

        /** @var $model Mage_Cms_Model_Page */
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

        $fieldset = $form->addFieldset('meta_fieldset', ['legend' => $helper->__('Meta Data'), 'class' => 'fieldset-wide']);

        $useStoreId = $fieldset->addField('page[meta][' . $store->getId() . '][use_store_id]', 'select', [
            'name'      => 'page[meta][' . $store->getId() . '][use_store_id]',
            'label'     => $helper->__('Use Store View'),
            'title'     => $helper->__('Use Store View'),
            'values'    => Mage::getSingleton('ho_cms/system_store')->getOptions($store->getId()),
            'value'     => $helper->getUseStoreIdValue($model, $store),
        ]);

        $keywords = $fieldset->addField('page[meta][' . $store->getId() . '][meta_keywords]', 'textarea', [
            'name'      => 'page[meta][' . $store->getId() . '][meta_keywords]',
            'label'     => $helper->__('Keywords'),
            'title'     => $helper->__('Meta Keywords'),
            'disabled'  => $isElementDisabled,
            'value'     => $model ? $model->getMetaKeywords() : '',
        ]);

        $description = $fieldset->addField('page[meta][' . $store->getId() . '][meta_description]', 'textarea', [
            'name'      => 'page[meta][' . $store->getId() . '][meta_description]',
            'label'     => $helper->__('Description'),
            'title'     => $helper->__('Meta Description'),
            'disabled'  => $isElementDisabled,
            'value'     => $model ? $model->getMetaDescription() : '',
        ]);

        /** @var Mage_Adminhtml_Block_Widget_Form_Element_Dependence $dependence */
        $dependence = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');
        $dependence
            ->addFieldMap($useStoreId->getHtmlId(), $useStoreId->getName())
            ->addFieldMap($keywords->getHtmlId(), $keywords->getName())
            ->addFieldMap($description->getHtmlId(), $description->getName())
            ->addFieldDependence($keywords->getName(), $useStoreId->getName(), $helper::TYPE_OWN_CONTENT)
            ->addFieldDependence($description->getName(), $useStoreId->getName(), $helper::TYPE_OWN_CONTENT)
        ;

        $this->setChild('form_after', $dependence);

        Mage::dispatchEvent('adminhtml_cms_page_edit_tab_meta_prepare_form', ['form' => $form]);

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
