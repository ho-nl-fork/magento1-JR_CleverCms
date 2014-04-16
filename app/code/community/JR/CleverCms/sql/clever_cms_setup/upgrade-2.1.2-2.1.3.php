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
 * @copyright   Copyright © 2014 H&O (http://www.h-o.nl/)
 * @license     H&O Commercial License (http://www.h-o.nl/license)
 * @author      Paul Hachmang – H&O <info@h-o.nl>
 *
 * 
 */
?>
<?php
/* @var $installer JR_CleverCms_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

if (Mage::getConfig()->getNode('modules/Mana_Seo')) {
    $installer->getConnection()->dropForeignKey(
        $installer->getTable('mana_seo/url'),
        'FK_m_seo_url_cms_page_id'
    );
    $installer->getConnection()->addForeignKey(
        'FK_m_seo_url_cms_page_id',
        $installer->getTable('mana_seo/url'),
        'cms_page_id',
        $installer->getTable('cms/page_tree'),
        'page_id'
    );
}

$installer->endSetup();