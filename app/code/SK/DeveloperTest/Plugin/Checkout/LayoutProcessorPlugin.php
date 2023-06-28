<?php
/**
 * Copyright © Magento All rights reserved.
 * See COPYING.txt for license details.
 * @author Sonali Kosrabe <sonalikosrabe@outlook.com>
 * @description  To block country from checkout while selecting countries
 */

declare(strict_types=1);

namespace SK\DeveloperTest\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use SK\DeveloperTest\Helper\Data as DataHelper;

/**
 * Class LayoutProcessorPlugin
 * To validate shipping country field
 */
class LayoutProcessorPlugin
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
     * Validate shipping country field
     *
     * @param LayoutProcessor $subject
     * @param array $result
     * @return array
     */
    public function afterProcess(
        LayoutProcessor $subject,
        array $result
    ): array {

        $moduleEnabled = $this->dataHelper->getConfigValue(DataHelper::MODULE_ENABLED);
        if ($moduleEnabled) {
            $result['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']
            ['children']['country_id']['validation']['blocked-country'] = true;
        }
        return $result;
    }
}
