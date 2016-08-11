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

class JR_CleverCms_Block_Adminhtml_Cms_Page_Edit_Tab_Content
    extends Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Content
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

        $fieldset = $form->addFieldset('content_fieldset', array('legend' => $helper->__('Content'), 'class' => 'fieldset-wide'));

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array('tab_id' => $this->getTabId())
        );

        $useStoreId = $fieldset->addField('page[content][' . $store->getId() . '][use_store_id]', 'select', [
            'name'      => 'page[content][' . $store->getId() . '][use_store_id]',
            'label'     => $helper->__('Use Store View'),
            'title'     => $helper->__('Use Store View'),
            'values'    => Mage::getSingleton('ho_cms/system_store')->getOptions($store->getId()),
            'value'     => $helper->getUseStoreIdValue($model, $store),
        ]);

        $heading = $fieldset->addField('page[content][' . $store->getId() . '][content_heading]', 'text', [
            'name'      => 'page[content][' . $store->getId() . '][content_heading]',
            'label'     => $helper->__('Content Heading'),
            'title'     => $helper->__('Content Heading'),
            'disabled'  => $isElementDisabled,
            'value'     => $model ? $model->getContentHeading() : '',
        ]);

        $content = $contentField = $fieldset->addField('page[content][' . $store->getId() . '][content]', 'editor', [
            'name'      => 'page[content][' . $store->getId() . '][content]',
            'style'     => 'height:36em;',
            'disabled'  => $isElementDisabled,
            'config'    => $wysiwygConfig,
            'value'     => $model ? $model->getContent() : '',
        ]);

        // Setting custom renderer for content field to remove label column
        $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
            ->setTemplate('cms/page/edit/form/renderer/content.phtml');
        $contentField->setRenderer($renderer);

        /** @var Mage_Adminhtml_Block_Widget_Form_Element_Dependence $dependence */
        $dependence = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');
        $dependence
            ->addFieldMap($useStoreId->getHtmlId(), $useStoreId->getName())
            ->addFieldMap($heading->getHtmlId(), $heading->getName())
            ->addFieldDependence($heading->getName(), $useStoreId->getName(), $helper::TYPE_OWN_CONTENT);

        // @todo Hide content field when Markdown is enabled, field dependency doesn't work
        if (! Mage::helper('core')->isModuleEnabled('SchumacherFM_Markdown')) {
            $dependence->addFieldDependence($content->getName(), $useStoreId->getName(), $helper::TYPE_OWN_CONTENT);
        }

        $this->setChild('form_after', $dependence);

        $this->setForm($form);

        Mage::dispatchEvent('adminhtml_cms_page_edit_tab_content_prepare_form', array('form' => $form));

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
