<?php
/**
 * Ho_Cms
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
 * @category    Ho
 * @package     Ho_Cms
 * @copyright   Copyright © 2016 H&O (http://www.h-o.nl/)
 * @license     H&O Commercial License (http://www.h-o.nl/license)
 * @author      Maikel Koek – H&O <info@h-o.nl>
 */

class JR_CleverCms_Block_Adminhtml_Cms_Page_Edit_Tab_Store extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * JR_CleverCms_Block_Adminhtml_Cms_Page_Edit_Tab_Store constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('block_content_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('jr_clevercms')->__('Page Information'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    /**
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _beforeToHtml()
    {
        $blocks = [
            'adminhtml/cms_page_edit_tab_main'      => 'main_section',
            'adminhtml/cms_page_edit_tab_content'   => 'content_section',
            'adminhtml/cms_page_edit_tab_design'    => 'design_section',
            'adminhtml/cms_page_edit_tab_meta'      => 'meta_section',
        ];

        if (Mage::registry('cms_page')->getId()) {
            foreach ($blocks as $blockModel => $name) {
                /** @var Mage_Adminhtml_Block_Widget_Tab_Interface $block */
                $block = $this->getLayout()->createBlock($blockModel);
                $this->addTab($name . '_store_' . $this->getStore()->getId(), [
                    'label' => Mage::helper('jr_clevercms')->__($block->getTabLabel()),
                    'title' => Mage::helper('jr_clevercms')->__($block->getTabLabel()),
                    'content' => $block->setStore($this->getStore())->toHtml(),
                ]);
            }
        }

        return parent::_beforeToHtml();
    }
}
