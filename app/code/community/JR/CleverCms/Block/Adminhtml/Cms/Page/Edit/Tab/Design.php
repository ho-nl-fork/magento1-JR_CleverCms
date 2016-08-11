<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class JR_CleverCms_Block_Adminhtml_Cms_Page_Edit_Tab_Design
    extends Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design
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

        $layoutFieldset = $form->addFieldset('layout_fieldset', [
            'legend' => $helper->__('Page Layout'),
            'class'  => 'fieldset-wide',
            'disabled'  => $isElementDisabled,
        ]);

        $useStoreId = $layoutFieldset->addField('page[design][' . $store->getId() . '][use_store_id]', 'select', [
            'name'      => 'page[design][' . $store->getId() . '][use_store_id]',
            'label'     => $helper->__('Use Store View'),
            'title'     => $helper->__('Use Store View'),
            'values'    => Mage::getSingleton('ho_cms/system_store')->getOptions($store->getId()),
            'value'     => $helper->getUseStoreIdValue($model, $store),
        ]);

        $rootTemplate = $layoutFieldset->addField('page[design][' . $store->getId() . '][root_template]', 'select', [
            'name'     => 'page[design][' . $store->getId() . '][root_template]',
            'label'    => $helper->__('Layouts'),
            'values'   => Mage::getSingleton('catalog/category_attribute_source_layout')->getAllOptions(),
            'disabled' => $isElementDisabled,
        ]);

        $layoutUpdate = $layoutFieldset->addField('page[design][' . $store->getId() . '][layout_update_xml]', 'textarea', [
            'name'      => 'page[design][' . $store->getId() . '][layout_update_xml]',
            'label'     => $helper->__('Layout Update XML'),
            'style'     => 'height:24em;',
            'disabled'  => $isElementDisabled,
        ]);

        $designFieldset = $form->addFieldset('design_fieldset', [
            'legend' => $helper->__('Custom Design'),
            'class'  => 'fieldset-wide',
            'disabled'  => $isElementDisabled,
        ]);

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
        );

        $customThemeFrom = $designFieldset->addField('page[design][' . $store->getId() . '][custom_theme_from]', 'date', [
            'name'      => 'page[design][' . $store->getId() . '][custom_theme_from]',
            'label'     => $helper->__('Custom Design From'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $dateFormatIso,
            'disabled'  => $isElementDisabled,
        ]);

        $customThemeTo = $designFieldset->addField('page[design][' . $store->getId() . '][custom_theme_to]', 'date', [
            'name'      => 'page[design][' . $store->getId() . '][custom_theme_to]',
            'label'     => $helper->__('Custom Design To'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $dateFormatIso,
            'disabled'  => $isElementDisabled,
        ]);

        $customTheme = $designFieldset->addField('page[design][' . $store->getId() . '][custom_theme]', 'select', [
            'name'      => 'page[design][' . $store->getId() . '][custom_theme]',
            'label'     => $helper->__('Custom Theme'),
            'values'    => Mage::getModel('core/design_source_design')->getAllOptions(),
            'disabled'  => $isElementDisabled,
        ]);

        $customRootTemplate = $designFieldset->addField('page[design][' . $store->getId() . '][custom_root_template]', 'select', [
            'name'      => 'page[design][' . $store->getId() . '][custom_root_template]',
            'label'     => $helper->__('Custom Layout'),
            'values'    => Mage::getSingleton('catalog/category_attribute_source_layout')->getAllOptions(),
            'disabled'  => $isElementDisabled,
        ]);

        $customLayoutUpdate = $designFieldset->addField('page[design][' . $store->getId() . '][custom_layout_update_xml]', 'textarea', [
            'name'      => 'page[design][' . $store->getId() . '][custom_layout_update_xml]',
            'label'     => $helper->__('Custom Layout Update XML'),
            'style'     => 'height:24em;',
            'disabled'  => $isElementDisabled,
        ]);

        /** @var Mage_Adminhtml_Block_Widget_Form_Element_Dependence $dependence */
        $dependence = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');
        $dependence
            ->addFieldMap($useStoreId->getHtmlId(), $useStoreId->getName())
            ->addFieldMap($rootTemplate->getHtmlId(), $rootTemplate->getName())
            ->addFieldMap($layoutUpdate->getHtmlId(), $layoutUpdate->getName())
            ->addFieldMap($customThemeFrom->getHtmlId(), $customThemeFrom->getName())
            ->addFieldMap($customThemeTo->getHtmlId(), $customThemeTo->getName())
            ->addFieldMap($customTheme->getHtmlId(), $customTheme->getName())
            ->addFieldMap($customRootTemplate->getHtmlId(), $customRootTemplate->getName())
            ->addFieldMap($customLayoutUpdate->getHtmlId(), $customLayoutUpdate->getName())
            ->addFieldDependence($rootTemplate->getName(), $useStoreId->getName(), $helper::TYPE_OWN_CONTENT)
            ->addFieldDependence($layoutUpdate->getName(), $useStoreId->getName(), $helper::TYPE_OWN_CONTENT)
            ->addFieldDependence($customThemeFrom->getName(), $useStoreId->getName(), $helper::TYPE_OWN_CONTENT)
            ->addFieldDependence($customThemeTo->getName(), $useStoreId->getName(), $helper::TYPE_OWN_CONTENT)
            ->addFieldDependence($customTheme->getName(), $useStoreId->getName(), $helper::TYPE_OWN_CONTENT)
            ->addFieldDependence($customRootTemplate->getName(), $useStoreId->getName(), $helper::TYPE_OWN_CONTENT)
            ->addFieldDependence($customLayoutUpdate->getName(), $useStoreId->getName(), $helper::TYPE_OWN_CONTENT)
        ;

        $this->setChild('form_after', $dependence);

        Mage::dispatchEvent('adminhtml_cms_page_edit_tab_design_prepare_form', ['form' => $form]);

        $this->setForm($form);

        return $this;
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
