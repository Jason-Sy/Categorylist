<?php
/**
 * @author Thijs Adriaansens BURO210
 * @copyright Copyright © 2019 BURO210. All rights reserved.
 * @package Buro210/CategoryList
 */

namespace Buro210\CategoryList\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{

    private $eavSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
    	$setup->startSetup();

    	/** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'additional_image',
            [
                'type' => 'varchar',
                'label' => 'List widget image',
                'input' => 'image',
                'sort_order' => 100,
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => null,
                'group' => 'General Information',
                'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image'
            ]
        );

        $setup->endSetup();
    }
}
