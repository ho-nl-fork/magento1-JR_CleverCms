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
 * @category  JR
 * @package   JR_CleverCms
 * @author    Paul Hachmang – H&O <info@h-o.nl>
 * @copyright 2015 Copyright © H&O (http://www.h-o.nl/)
 * @license   H&O Commercial License (http://www.h-o.nl/license)
 */

class JR_CleverCms_Model_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     */
    public function addCleverCmsNodes(Varien_Event_Observer $observer)
    {
        /** @var Varien_Data_Tree_Node $menu */
        $menu = $observer->getData('menu');

        $block = $observer->getEvent()->getBlock();
        $block->addCacheTag(Mage_Cms_Model_Page::CACHE_TAG);

        if (Mage::getStoreConfigFlag('cms/clever/show_homepage_link')) {
            $this->_addHomePageToMenu($menu, $block, true);
        }

        $this->_addCmsPagesToMenu(
            $this->_getChildren($this->getCmsRootPage()), $menu, $block, true
        );

        $this->_addCmsPagesToMenu(
            $this->_getChildren($this->getCmsRootPage(0)), $menu, $block, true
        );
    }


    /**
     * Recursively adds cms pages to top menu
     *
     * @param Varien_Data_Tree_Node $parentNode
     * @param Mage_Page_Block_Html_Topmenu $menuBlock
     * @param bool $addTags
     */
    protected function _addHomePageToMenu($parentNode, $menuBlock, $addTags = false)
    {
        $tree = $parentNode->getTree();

        $request = Mage::app()->getRequest();

        $isActive = $request->getModuleName() == 'cms'
                   && $request->getControllerName() == 'index'
                   && $request->getActionName() == 'index';

        $pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE);

        $nodeData = array(
            'name'      => $menuBlock->__('Home'),
            'id'        => 'cms-node-'.$pageId,
            'url'       => Mage::getBaseUrl(),
            'is_active' => $isActive
        );
        $categoryNode = new Varien_Data_Tree_Node($nodeData, 'id', $tree, $parentNode);

        $currentItems = array();
        foreach ($parentNode->getChildren() as $child) {
            $currentItems[] = $child;
            $parentNode->removeChild($child);
        }

        $parentNode->addChild($categoryNode);

        foreach ($currentItems as $child){
            $parentNode->addChild($child);
        }
    }


    /**
     * @param JR_CleverCms_Model_Cms_Page $cmsPage
     * @return JR_CleverCms_Model_Resource_Cms_Page_Collection
     */
    protected function _getChildren(JR_CleverCms_Model_Cms_Page $cmsPage)
    {
        $childCollection = $cmsPage->getChildren();
        $childCollection->addFieldToFilter('is_active', 1);
        $childCollection->addFieldToFilter('include_in_menu', 1);

        if (Mage::helper('cms/page')->isPermissionsEnabled(Mage::app()->getStore())) {
            $childCollection->addPermissionsFilter($this->getCustomerGroupId());
        }

//        if ($level = Mage::getStoreConfig('catalog/navigation/max_depth')) {
//            $childCollection->addFieldToFilter('level', array('lteq' => $level + 1));
//        }

        return $childCollection;
    }


    /**
     * Recursively adds cms pages to top menu
     *
     * @param JR_CleverCms_Model_Resource_Cms_Page_Collection $cmsPages
     * @param Varien_Data_Tree_Node $parentNode
     * @param Mage_Page_Block_Html_Topmenu $menuBlock
     * @param bool $addTags
     */
    protected function _addCmsPagesToMenu($cmsPages, $parentNode, $menuBlock, $addTags = false)
    {
        foreach ($cmsPages as $cmsPage) {
            /** @var JR_CleverCms_Model_Cms_Page $cmsPage */
            $nodeId = 'cms-node-' . $cmsPage->getId();

            if ($addTags) {
                $menuBlock->addModelTags($cmsPage);
            }

            $tree = $parentNode->getTree();
            $nodeData = array(
                'name'      => $cmsPage->getTitle(),
                'id'        => $nodeId,
                'class'     => $cmsPage->getIdentifier(),
                'url'       => $cmsPage->getUrl(),
                'is_active' => $this->_isActiveMenuCmsPage($cmsPage)
            );
            $categoryNode = new Varien_Data_Tree_Node($nodeData, 'id', $tree, $parentNode);
            $parentNode->addChild($categoryNode);

            $this->_addCmsPagesToMenu($this->_getChildren($cmsPage), $categoryNode, $menuBlock, $addTags);
        }
    }


    /**
     * Checks whether category belongs to active category's path
     *
     * @param JR_CleverCms_Model_Cms_Page $cmsPage
     * @return bool
     */
    protected function _isActiveMenuCmsPage($cmsPage)
    {
        return Mage::registry('current_page') && Mage::registry('current_page')->getId() == $cmsPage->getId();
    }


    /**
     * Get top level CMS pages of current store
     *
     * @return array of Mage_Cms_Model_Page
     */
    public function getStoreCmsPages()
    {
        $collection = $this->getCmsRootPage(0)->getChildren();
        foreach ($this->getCmsRootPage()->getChildren() as $page) {
            $collection->addItem($page);
        }

        return $collection;
    }


    /**
     * Return the root CMS page for this store
     *
     * @param null|int $storeId
     * @return JR_CleverCms_Model_Cms_Page
     */
    public function getCmsRootPage($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
        return Mage::getModel('cms/page')->loadRootByStoreId($storeId);
    }
}
