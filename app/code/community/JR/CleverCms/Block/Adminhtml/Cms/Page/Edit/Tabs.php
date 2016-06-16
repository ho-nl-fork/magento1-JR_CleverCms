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
 * @category    Ho
 * @package     JR_CleverCms
 * @copyright   Copyright © 2016 H&O (http://www.h-o.nl/)
 * @license     H&O Commercial License (http://www.h-o.nl/license)
 * @author      Maikel Koek – H&O <info@h-o.nl>
 */

class JR_CleverCms_Block_Adminhtml_Cms_Page_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * JR_CleverCms_Block_Adminhtml_Cms_Page_Edit_Tabs constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('block_store_tabs');
        $this->setDestElementId('none');
        $this->setTitle(Mage::helper('jr_clevercms')->__('Store Views'));
    }

    /**
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _beforeToHtml()
    {
        if (Mage::registry('cms_page')->getId()) {
            foreach (Mage::app()->getStores() as $store) {
                /** @var Mage_Core_Model_Store $store */
                $this->addTab('store_' . $store->getId(), [
                    'label' => Mage::helper('jr_clevercms')->__($store->getName() . ' (' . $store->getWebsite()->getName() . ')'),
                    'title' => Mage::helper('jr_clevercms')->__($store->getName() . ' (' . $store->getWebsite()->getName() . ')'),
                    'content' => $this->getLayout()->createBlock('jr_clevercms/adminhtml_cms_page_edit_tab_store')->setStore($store)->toHtml(),
                ]);
            }
        }

        return parent::_beforeToHtml();
    }
}
