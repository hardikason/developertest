<?php
/**
 * Copyright © Magento All rights reserved.
 * See COPYING.txt for license details.
 * @author Sonali Kosrabe <sonalikosrabe@outlook.com>
 */

declare(strict_types=1);

namespace SK\DeveloperTest\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\StorageInterface;
use SK\DeveloperTest\Model\CustomerLocation;
use SK\DeveloperTest\Helper\Data as DataHelper;
use Magento\Framework\Message\ManagerInterface as MessageManager;

/**
 * Class LayoutLoadBeforeObserver
 * To set customer remote address in session storage
 */
class LayoutLoadBeforeObserver implements ObserverInterface
{

    /**
     * @var StorageInterface
     */
    protected $storageInterface;
    /**
     * @var CustomerLocation
     */
    protected $customerLocation;
    /**
     * @var DataHelper
     */
    protected $dataHelper;
    /**
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * @param StorageInterface $storageInterface
     * @param CustomerLocation $customerLocation
     * @param DataHelper $dataHelper
     * @param MessageManager $messageManager
     */
    public function __construct(
        StorageInterface $storageInterface,
        CustomerLocation $customerLocation,
        DataHelper $dataHelper,
        MessageManager $messageManager
    ) {
        $this->storageInterface = $storageInterface;
        $this->customerLocation = $customerLocation;
        $this->dataHelper = $dataHelper;
        $this->messageManager = $messageManager;
    }

    /**
     * Set customer remote address in session storage
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        try {
            $moduleEnabled = $this->dataHelper->getConfigValue(DataHelper::MODULE_ENABLED);

            $customerLocationInfo = '';
            if ($moduleEnabled) {
                $customerLocationInfo = $this->storageInterface->getData('customer_location');

                $decodedResponse = $this->dataHelper->unserializeData($customerLocationInfo);

                if (!$customerLocationInfo
                    || (isset($decodedResponse['success']) && $decodedResponse['success'] == false)) {
                    // get customer country info
                    $customerLocationInfo = $this->customerLocation->getCustomerLocation();
                }
            }
            $this->storageInterface->setData('customer_location', $customerLocationInfo);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }
    }
}
