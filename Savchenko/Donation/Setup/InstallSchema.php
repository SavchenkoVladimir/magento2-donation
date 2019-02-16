<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to peers.rom@gmail.com so we can send you a copy immediately.
 *
 * @category Savchenko
 * @package Savchenko_Donation
 * @copyright Copyright (c) Vladimir Savchenko
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Savchenko\Donation\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Savchenko\Donation\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $installer = $setup->getConnection();
        $columnName = 'donation_amount';
        $tableNames = [
            'quote',
            'sales_order',
            'sales_invoice'
        ];

        foreach ($tableNames as $tableName) {
            if ($installer->tableColumnExists($tableName, $columnName) !== false) {
                continue;
            }

            $installer->addColumn(
                $setup->getTable($tableName),
                $columnName,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0.0000',
                    'comment' => 'Donation amount'
                ]
            );
        }

        if (!$installer->isTableExists('savchenko_donation_setup')) {

            $table = $setup->getConnection()->newTable(
                $setup->getTable('savchenko_donation_setup')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true
                ],
                'Entity ID'
            )->addColumn(
                'is_enabled',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                [
                    'nullable' => false,
                    'default' => 0
                ],
                'Is Active'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'default' => '0'
                ],
                'Store id'
            )->addColumn(
                'image_url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Image url'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Title'
            )->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Description'
            )->addColumn(
                'donation_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Donation amount'
            );

            $installer->createTable($table);
        }

        $setup->endSetup();
    }
}
