<?php
/**
 * Copyright © Magento All rights reserved.
 * See COPYING.txt for license details.
 * @author Sonali Kosrabe <sonalikosrabe@outlook.com>
 * @description  To restrict product to order from specific countries before placing order
 */
declare(strict_types=1);

namespace SK\DeveloperTest\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use SK\DeveloperTest\Helper\Data as DataHelper;

/**
 * Class OrderPlaceBefore
 * To check condition before placing order
 */
class OrderPlaceBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var  ProductRepositoryInterface
     */
    protected $productRepositoryInterface;
    /**
     * @var CountryInformationAcquirerInterface
     */
    protected $countryInformationAcquirerInterface;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param CountryInformationAcquirerInterface $countryInformationAcquirerInterface
     * @param ManagerInterface $messageManager
     * @param DataHelper $dataHelper
     */
    public function __construct(
        ProductRepositoryInterface $productRepositoryInterface,
        CountryInformationAcquirerInterface $countryInformationAcquirerInterface,
        ManagerInterface $messageManager,
        DataHelper $dataHelper
    ) {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->countryInformationAcquirerInterface = $countryInformationAcquirerInterface;
        $this->messageManager = $messageManager;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Check condition before placing order
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $moduleEnabled = $this->dataHelper->getConfigValue(DataHelper::MODULE_ENABLED);
        if ($moduleEnabled) {
            try {
                $items = $observer->getEvent()->getOrder()->getAllItems();
                $order = $observer->getEvent()->getOrder();
                $shippingAddress = $order->getShippingAddress();

                if ($shippingAddress) {
                    $countryCode = $shippingAddress->getCountryId();
                    $countryInfo = $this->countryInformationAcquirerInterface->getCountryInfo($countryCode);
                    $count = 0;
                    foreach ($items as $item) {

                        $product = $this->productRepositoryInterface->getById($item->getProductId());
                        $customerCountryCode = isset($countryCode) ? strtoupper($countryCode) : null;
                        $productBlockedFromCountries = $product->getData('blocked_countries') ? explode(
                            ',',
                            $product->getData('blocked_countries')
                        ) : [];

                        // check if selected country in list of product block countries
                        if ($customerCountryCode && $productBlockedFromCountries && in_array(
                            $customerCountryCode,
                            $productBlockedFromCountries
                        )) {
                            $count++;
                            $this->messageManager->addWarningMessage(__($product->getName()));
                        }
                    }

                    if ($count) {
                        throw new LocalizedException(__(
                            "You can\'t order some product(s) in %1. Please check items added in cart.",
                            $countryInfo->getFullNameLocale()
                        ));
                    }
                }
            } catch (LocalizedException $e) {
                throw new LocalizedException(__($e->getMessage()));
            }
        }
    }
}
