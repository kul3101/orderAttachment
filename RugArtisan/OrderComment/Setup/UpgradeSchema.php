<?php
/**
 * Upgrade schema class
 *
 * @author  Kuldip Chudasama
 * @package RugArtisan_OrderComment
 */

namespace RugArtisan\OrderComment\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package RugArtisan\OrderComment\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /** added new field to store images */
        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_status_history'),
                'media',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'media',
                ]
            );
        }
        $installer->endSetup();
    }
}
