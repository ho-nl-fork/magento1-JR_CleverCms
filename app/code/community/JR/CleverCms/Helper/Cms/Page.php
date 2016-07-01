<?php

class JR_CleverCms_Helper_Cms_Page extends Mage_Cms_Helper_Page
{
    const TYPE_DEFAULT_CONTENT  = 'default';
    const TYPE_OWN_CONTENT      = 'own';

    public function renderPage(Mage_Core_Controller_Front_Action $action, $pageId = null)
    {
        $storeId = Mage::app()->getStore()->getId();
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        if (! $this->isAllowed($storeId, $customerGroupId, $pageId)) {
            return false;
        }

        return parent::renderPage($action, $pageId);
    }

    public function isAllowed($storeId, $customerGroupId, $pageId)
    {
        $page = Mage::getModel('cms/page')->load($pageId);
        if ($page->getStoreId() == 0 && !in_array($storeId, $page->getStores())) {
            return false;
        }

        if (! $this->isPermissionsEnabled($storeId)) {
            return true;
        }

        return Mage::getResourceModel('cms/page_permission')->exists($storeId, $customerGroupId, $pageId);
    }

    public function isPermissionsEnabled($store = null)
    {
        return Mage::getStoreConfigFlag('cms/clever/permissions_enabled', Mage::app()->getStore($store));
    }

    public function isCreatePermanentRedirects($store = null)
    {
        return Mage::getStoreConfigFlag('cms/clever/save_rewrites_history', Mage::app()->getStore($store));
    }

    /**
     * @param Mage_Cms_Model_Page $page
     * @param Mage_Core_Model_Store $store
     * @return false|Mage_Cms_Model_Page
     */
    public function getPage($page, $store)
    {
        if (is_numeric($store)) {
            $store = Mage::app()->getStore($store);
        }

        $collection = Mage::getModel('cms/page')
            ->getCollection()
            ->addFieldToFilter('identifier', $page->getIdentifier());

        $collection->getSelect()->where('FIND_IN_SET(?, store_ids)', $store->getId());

        if ($collection->getFirstItem()->getId()) {
            return $collection->getFirstItem();
        }

        return false;
    }

    /**
     * @param false|Mage_Cms_Model_Page $page
     * @param Mage_Core_Model_Store $store
     * @return int|string
     */
    public function getUseStoreIdValue($page, $store)
    {
        if (!$page) {
            return self::TYPE_DEFAULT_CONTENT; // Use default
        }

        if ($page->getData('store_ids')) {
            $storeIds = explode(',', $page->getData('store_ids'));
            if ($store->getId() == $storeIds[0]) {
                return self::TYPE_OWN_CONTENT;
            }
            elseif (in_array($store->getId(), $storeIds)) {
                return $storeIds[0];
            }
        }

        return self::TYPE_OWN_CONTENT;
    }
}
