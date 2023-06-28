<?php
/**
 * Copyright © Magento All rights reserved.
 * See COPYING.txt for license details.
 * @author Sonali Kosrabe <sonalikosrabe@outlook.com>
 * @description Add check on Proceed to Checkout
 */

declare(strict_types=1);

namespace SK\DeveloperTest\Plugin\Checkout\Controller;

use Magento\Framework\Message\ManagerInterface;
use Magento\Checkout\Controller\Cart\Index;
use Magento\Checkout\Model\Session as CheckoutSession;
use SK\DeveloperTest\Helper\Data as DataHelper;

/**
 * Class CartErrorMessagePlugin
 * To check if products are allowed to checkout
 */
class CartErrorMessagePlugin
{

    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @param ManagerInterface $messageManager
     * @param CheckoutSession $checkoutSession
     * @param DataHelper $dataHelper
     */
    public function __construct(
        ManagerInterface $messageManager,
        CheckoutSession $checkoutSession,
        DataHelper $dataHelper
    ) {
        $this->messageManager = $messageManager;
        $this->checkoutSession = $checkoutSession;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Check if products are allowed to checkout
     *
     * @param Index $subject
     * @param \Closure $proceed
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function aroundExecute(
        Index $subject,
        \Closure $proceed
    ) {

        $moduleEnabled = $this->dataHelper->getConfigValue(DataHelper::MODULE_ENABLED);
        $checkoutErrorMessage = $this->dataHelper->getConfigValue(DataHelper::CHECKOUT_ERROR_MESSAGE);
        if ($moduleEnabled) {
            $items = $this->checkoutSession->getQuote()->getAllItems();
            $shippingAddress = $this->checkoutSession->getQuote()->getShippingAddress();

            if ($shippingAddress && $items) {
                $result = $this->dataHelper->checkProducts($items, $shippingAddress);

                if ($result['productCount']) {
                    foreach ($result['productNames'] as $productName) {
                        $this->messageManager->addErrorMessage(__(
                            $checkoutErrorMessage,
                            $productName,
                            $result['countryName']
                        ));
                    }
                }
            }
        }

        return $proceed();
    }
}
