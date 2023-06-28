<?php
/**
 * Copyright © Magento All rights reserved.
 * See COPYING.txt for license details.
 * @author Sonali Kosrabe <sonalikosrabe@outlook.com>
 * @description  To block country from checkout while selecting countries
 */

declare(strict_types=1);

namespace SK\DeveloperTest\Controller\Index;

use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class Index
 * To check selected country allowed to checkout product
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var  JsonSerializer
     */
    protected $jsonSerializer;
    /**
     * @var  CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var  ProductRepositoryInterface
     */
    protected $productRepositoryInterface;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param JsonSerializer $jsonSerializer
     * @param CheckoutSession $checkoutSession
     * @param ProductRepositoryInterface $productRepositoryInterface
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        JsonSerializer $jsonSerializer,
        CheckoutSession $checkoutSession,
        ProductRepositoryInterface $productRepositoryInterface
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->checkoutSession = $checkoutSession;
        $this->productRepositoryInterface = $productRepositoryInterface;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $allowed = true;
        $countryCode = $this->getRequest()->getParam('countryCode');
        $quoteItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
        foreach ($quoteItems as $quoteItem) {
            $product = $this->productRepositoryInterface->getById($quoteItem->getProductId());

            $customerCountryCode = isset($countryCode) ? strtoupper($countryCode) : null;
            $productBlockedFromCountries = $product->getData('blocked_countries') ? explode(
                ',',
                $product->getData('blocked_countries')
            ) : [];
            // check if customer country in list of product block countries
            if ($customerCountryCode && $productBlockedFromCountries && in_array(
                $customerCountryCode,
                $productBlockedFromCountries
            )) {
                $allowed = false;
            }
        }

        return $this->jsonResponse([
            'allowed' => $allowed
        ]);
    }

    /**
     * Create json response
     *
     * @param string $response
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonSerializer->serialize($response)
        );
    }
}
