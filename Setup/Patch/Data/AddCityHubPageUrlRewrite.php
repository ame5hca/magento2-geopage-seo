<?php

namespace GiftGroup\GeoPage\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use GiftGroup\GeoPage\Model\UrlRewriteManager;
use GiftGroup\GeoPage\Model\Config;

class AddCityHubPageUrlRewrite implements DataPatchInterface,PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    
    /**
     * @var UrlRewriteManager
     */
    private $urlRewriteManager;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param UrlRewriteManager $urlRewriteManager
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        UrlRewriteManager $urlRewriteManager
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->urlRewriteManager = $urlRewriteManager;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        
        $this->urlRewriteManager->addStoreRewrite(Config::CITY_HUB_PAGE_URL, Config::CITY_HUB_PAGE_TARGET_URL, 0);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        /**
         * This is dependency to another patch. Dependency should be applied first
         * One patch can have few dependencies
         * Patches do not have versions, so if in old approach with Install/Ugrade data scripts you used
         * versions, right now you need to point from patch with higher version to patch with lower version
         * But please, note, that some of your patches can be independent and can be installed in any sequence
         * So use dependencies only if this important for you
         */
        return [];
    }

    public function revert()
    {
        //Here should go code that will revert all operations from `apply` method
        //Please note, that some operations, like removing data from column, that is in role of foreign key reference
        //is dangerous, because it can trigger ON DELETE statement
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        /**
         * This internal Magento method, that means that some patches with time can change their names,
         * but changing name should not affect installation process, that's why if we will change name of the patch
         * we will add alias here
         */
        return [];
    }
}
