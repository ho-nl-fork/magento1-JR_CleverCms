<?php
/** 
 * @category    Mana
 * @package     Mana_Seo
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */
/**
 * @author Mana Team
 *
 */
class JR_CleverCms_Model_Resource_Mana_Seo_UrlIndexer_CmsPage extends Mana_Seo_Resource_UrlIndexer_CmsPage {
    /**
     * @param Mana_Seo_Model_UrlIndexer $indexer
     * @param Mana_Seo_Model_Schema $schema
     * @param array $options
     */
    public function process($indexer, $schema, $options) {
        if (!isset($options['cms_page_id']) && !isset($options['store_id']) &&
            !isset($options['schema_global_id']) && !isset($options['schema_store_id']) && !$options['reindex_all']
        ) {
            return;
        }

        $db = $this->_getWriteAdapter();

        $fields = array(
            'url_key' => new Zend_Db_Expr('`p`.`identifier`'),
            'type' => new Zend_Db_Expr("'cms_page'"),
            'is_page' => new Zend_Db_Expr('1'),
            'is_parameter' => new Zend_Db_Expr('0'),
            'is_attribute_value' => new Zend_Db_Expr('0'),
            'is_category_value' => new Zend_Db_Expr('0'),
            'schema_id' => new Zend_Db_Expr($schema->getId()),
            'cms_page_id' => new Zend_Db_Expr('`p`.`page_id`'),
            'unique_key' => new Zend_Db_Expr("CONCAT(`p`.`page_id`, '-', `p`.`identifier`)"),
            'status' => new Zend_Db_Expr("IF(`p`.`is_active`, '" .
                Mana_Seo_Model_Url::STATUS_ACTIVE . "', '".
                Mana_Seo_Model_Url::STATUS_DISABLED . "')"),
            'description' => new Zend_Db_Expr(
                "CONCAT('{$this->seoHelper()->__('CMS page')} \\'', " .
                "`p`.`title`, '\\' (ID ', `p`.`page_id`, ')')"),
        );

        /* @var $select Varien_Db_Select */
        $select = $db->select()
            ->distinct()
            ->from(array('p' => $this->getTable('cms/page_tree')), null)
            ->columns($fields)
            ->where('`p`.`store_id` = ? OR `p`.`store_id` = 0', $schema->getStoreId())
            ->where('`p`.`identifier` <> \'\'');

        $obsoleteCondition = "(`schema_id` = " . $schema->getId() . ") AND (`is_page` = 1) AND (`type` = 'cms_page')";
        if (isset($options['cms_page_id'])) {
            $select->where('`p`.`page_id` = ?', $options['cms_page_id']);
            $obsoleteCondition .= ' AND (`cms_page_id` = ' . $options['cms_page_id'] . ')';
        }

        // convert SELECT into UPDATE which acts as INSERT on DUPLICATE unique keys
        $this->logger()->logUrlIndexer('-----------------------------');
        $this->logger()->logUrlIndexer(get_class($this));
        $this->logger()->logUrlIndexer($select->__toString());
        $this->logger()->logUrlIndexer($schema->getId());
        $this->logger()->logUrlIndexer($obsoleteCondition);
        $this->logger()->logUrlIndexer(json_encode($options));
        $sql = $select->insertFromSelect($this->getTargetTableName(), array_keys($fields));

        // run the statement
        $this->makeAllRowsObsolete($options, $obsoleteCondition);
        $db->exec($sql);
    }
}
