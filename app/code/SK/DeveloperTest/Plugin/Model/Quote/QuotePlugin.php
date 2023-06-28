<?php
/**
 * Copyright © Magento All rights reserved.
 * See COPYING.txt for license details.
 * @author Sonali Kosrabe <sonalikosrabe@outlook.com>
 * @description  To restrict product from adding to cart if not allowed to order from specific countries
 */

namespace SK\DeveloperTest\Plugin\Model\Quote;

use Magento\Framework\Exception\LocalizedException;
use SK\DeveloperTest\Helper\Data as DataHelper;

/**
 * Class QuotePlugin
 * To check product allow to checkout in customer current location
 */
class QuotePlugin
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /***
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Check product allow to checkout in customer current location
     *
     * @param \Magento\Quote\Model\Quote $subject
     * @param mixed $productInfo
     * @param string|null $requestInfo
     * @param string $processMode
     * @return array|void
     * @throws LocalizedException
     */
    public function beforeAddProduct(
        \Magento\Quote\Model\Quote $subject,
        $productInfo,
        $requestInfo = null,
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
    ) {

        $moduleEnabled = $this->dataHelper->getConfigValue(DataHelper::MODULE_ENABLED);
        $error_message = $this->dataHelper->getConfigValue(DataHelper::ERROR_MESSAGE);

        if ($moduleEnabled) {
            try {

                $customerCountryInfo = $this->dataHelper->getCustomerLocationCountryInfo();
                $countryCode = $customerCountryInfo['countryCode'];
                $countryName = $customerCountryInfo['countryName'];

                $productBlockedFromCountries = $productInfo->getBlockedCountries() ? explode(
                    ',',
                    $productInfo->getBlockedCountries()
                ) : [];

                // check if customer country in list of product block countries
                if ($countryCode && $productBlockedFromCountries && in_array(
                    $countryCode,
                    $productBlockedFromCountries
                )) {
                    //if yes display error message
                    throw new LocalizedException(__($error_message, $countryName));
                }

            } catch (\Exception $e) {
                throw new LocalizedException(__($e->getMessage()));
            }
        }

        return [$productInfo, $requestInfo, $processMode];
    }
}
