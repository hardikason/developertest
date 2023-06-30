<?php
namespace SK\DeveloperTest\Helper;

/**
 * Copyright © Magento All rights reserved.
 * See COPYING.txt for license details.
 * @author Sonali Kosrabe <sonalikosrabe@outlook.com>
 * @description  Get config values
 */

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\StorageInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * To get all config information
 */
class Data extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var  ProductRepositoryInterface
     */
    protected $productRepositoryInterface;
    /**
     * @var CountryInformationAcquirerInterface
     */
    protected $countryInformationAcquirerInterface;
    /**
     * @var StorageInterface
     */
    protected $storageInterface;
    /**
     * @var JsonSerializer
     */
    protected $jsonSerializer;
    /**
     * module enable config path
     */
    public const MODULE_ENABLED = 'developertest/general/enabled';
    /**
     * error message config path
     */
    public const ERROR_MESSAGE = 'developertest/general/error_message';
    /**
     * error message config path
     */
    public const CHECKOUT_ERROR_MESSAGE = 'developertest/general/checkout_error_message';
    /**
     * api url config path
     */
    public const API_URL = 'developertest/api_details/url';
    /**
     * api access key config path
     */
    public const API_ACCESS_KEY = 'developertest/api_details/access_key';
    /**
     * test specific ip config path
     */
    public const BLOCK_SPECIFIC_IP = 'developertest/api_details/block_specific_ip';

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param CountryInformationAcquirerInterface $countryInformationAcquirerInterface
     * @param StorageInterface $storageInterface
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        ScopeConfigInterface $scopeConfig,
        ProductRepositoryInterface $productRepositoryInterface,
        CountryInformationAcquirerInterface $countryInformationAcquirerInterface,
        StorageInterface $storageInterface,
        JsonSerializer $jsonSerializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->countryInformationAcquirerInterface = $countryInformationAcquirerInterface;
        $this->storageInterface = $storageInterface;
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($context);
    }

    /**
     * To get config values
     *
     * @param string $path
     * @return mixed
     */
    public function getConfigValue($path)
    {

        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue($path, $storeScope);
    }

    /**
     * To unserialize data
     *
     * @param string $data
     * @return array|bool|float|int|mixed|string|null
     */
    public function unserializeData($data)
    {
        return $this->jsonSerializer->unserialize($data);
    }

    /**
     * To get customer current location
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCustomerLocationCountryInfo()
    {
        $customerLocation = $this->storageInterface->getData('customer_location');
        if ($customerLocation) {
            $customerLocation = $this->jsonSerializer->unserialize($customerLocation);
            $countryCode = isset($customerLocation['country_code'])
                                ? strtoupper($customerLocation['country_code']): null;
            $countryInfo = $this->countryInformationAcquirerInterface->getCountryInfo($countryCode);

            return [
                'countryCode' => $countryCode,
                'countryName' => $countryInfo->getFullNameLocale()
            ];
        }

        return [
            'countryCode' => '',
            'countryName' => ''
        ];
    }

    /**
     * To check if products blocked from selected country
     *
     * @param array $items
     * @param array|null $shippingAddress
     * @return array
     * @throws NoSuchEntityException
     */
    public function checkProducts($items, $shippingAddress = null)
    {

        $customerCountryInfo = $this->getCustomerLocationCountryInfo();
        $countryCode = $customerCountryInfo['countryCode'];
        $countryName = $customerCountryInfo['countryName'];

        if ($shippingAddress && $shippingAddress->getData('country_code')) {
            $countryCode = $shippingAddress->getData('country_code');
            $countryInfo = $this->countryInformationAcquirerInterface->getCountryInfo($countryCode);
            $countryName = $countryInfo->getFullNameLocale();
        }

        $count = 0;
        $productNames = [];

        if ($countryCode) {

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

                    $itemName = $item->getName();
                    if ($product->getTypeId() == 'configurable') {
                        $_itemOptions = $item->getProduct()->getTypeInstance(true)
                                                ->getOrderOptions($item->getProduct());
                        $itemName = $_itemOptions['simple_name'];
                    }
                    $productNames[] = $itemName;
                }
            }
        }

        return [
                'productCount' => $count,
                'productNames' => $productNames,
                'countryName'  => $countryName
            ];
    }
}
