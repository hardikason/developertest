<?php
/**
 * Copyright © Magento All rights reserved.
 * See COPYING.txt for license details.
 * @author Sonali Kosrabe <sonalikosrabe@outlook.com>
 */

namespace SK\DeveloperTest\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * class CreateProductAttribute
 * To create new product attribute
 */
class CreateProductAttribute implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'blocked_countries');

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'blocked_countries',
            [
                'type' => 'text',
                'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                'frontend' => '',
                'label' => 'Block Product From Countries',
                'input' => 'multiselect',
                'group' => 'Restricted Countries Setting',
                'class' => 'product_blocked_countries',
                'source' => \SK\DeveloperTest\Model\Config\Source\CountriesDropdown::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' =>    true,
                'required' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false
            ]
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.0';
    }
}
