<?php
/**
 * Copyright © Magento All rights reserved.
 * See COPYING.txt for license details.
 * @author Sonali Kosrabe <sonalikosrabe@outlook.com>
 * @description Add check on Proceed to Checkout
 */

declare(strict_types=1);

namespace SK\DeveloperTest\Plugin\Checkout\Controller;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\UrlFactory;
use Magento\Checkout\Controller\Index\Index;
use Magento\Checkout\Model\Session as CheckoutSession;
use SK\DeveloperTest\Helper\Data as DataHelper;

/**
 * Class ProceedToCheckoutPlugin
 * To check if products are allowed to checkout
 */
class ProceedToCheckoutPlugin
{
    /**
     * @var UrlFactory
     */
    protected $urlModel;
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @param UrlFactory $urlFactory
     * @param RedirectFactory $redirectFactory
     * @param CheckoutSession $checkoutSession
     * @param DataHelper $dataHelper
     */
    public function __construct(
        UrlFactory $urlFactory,
        RedirectFactory $redirectFactory,
        CheckoutSession $checkoutSession,
        DataHelper $dataHelper
    ) {
        $this->urlModel = $urlFactory;
        $this->resultRedirectFactory = $redirectFactory;
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
        if ($moduleEnabled) {

            if ($this->checkoutSession->getQuote()->getShippingAddress()) {
                $items = $this->checkoutSession->getQuote()->getAllItems();
                $shippingAddress = $this->checkoutSession->getQuote()->getShippingAddress();
                $result = $this->dataHelper->checkProducts($items, $shippingAddress);

                if ($result['productCount']) {
                    // code for redirect to cart page with error message
                    $this->urlModel = $this->urlModel->create();
                    $cartPageUrl = $this->urlModel->getUrl('checkout/cart/', ['_secure' => true]);
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setUrl($cartPageUrl);
                }
            }
        }

        return $proceed();
    }
}
