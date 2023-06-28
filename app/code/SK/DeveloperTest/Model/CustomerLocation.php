<?php
/**
 * Copyright © Magento All rights reserved.
 * See COPYING.txt for license details.
 * @author Sonali Kosrabe <sonalikosrabe@outlook.com>
 * @description  To retrieve customer location
 */

namespace SK\DeveloperTest\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\Curl;
use SK\DeveloperTest\Helper\Data as DataHelper;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Class CustomerLocation
 * To get current location of customer
 */
class CustomerLocation
{

    /**
     * @var Curl
     */
    protected $curl;
    /**
     * @var DataHelper
     */
    protected $dataHelper;
    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;
    /**
     * @var EncryptorInterface
     */
    protected $encryptorInterface;

    /**
     * @param Curl $curl
     * @param DataHelper $dataHelper
     * @param RemoteAddress $remoteAddress
     * @param EncryptorInterface $encryptorInterface
     */
    public function __construct(
        Curl $curl,
        DataHelper $dataHelper,
        RemoteAddress $remoteAddress,
        EncryptorInterface $encryptorInterface
    ) {
        $this->curl = $curl;
        $this->dataHelper = $dataHelper;
        $this->remoteAddress = $remoteAddress;
        $this->encryptorInterface = $encryptorInterface;
    }

    /**
     * Get Customer location
     *
     * @return string
     * @throws LocalizedException
     */
    public function getCustomerLocation()
    {
        try {
            $apiUrl = $this->dataHelper->getConfigValue(DataHelper::API_URL);
            $apiAccessKey = $this->dataHelper->getConfigValue(DataHelper::API_ACCESS_KEY);
            $apiAccessKey = $this->encryptorInterface->decrypt($apiAccessKey);
            $ipAddress = $this->dataHelper->getConfigValue(DataHelper::BLOCK_SPECIFIC_IP);

            if (!$ipAddress) {
                $ipAddress = $this->getClientIp();
            }

            //Append access key to url
            $apiUrl = $apiUrl . $ipAddress . "?access_key=" . $apiAccessKey;

            // get method
            $this->curl->get($apiUrl);
            // output of curl request
            $response = $this->curl->getBody();

            $decodedResponse = $this->dataHelper->unserializeData($response);
            if (isset($decodedResponse['success']) && $decodedResponse['success'] == false) {
                throw new LocalizedException(
                    __('IP Configuration Access Key Error : '.$decodedResponse['error']['info'])
                );
            }

            return $response;
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * Get ip address of customer
     *
     * @return false|string
     */
    private function getClientIp()
    {
        $ipAddress = $this->remoteAddress->getRemoteAddress();
        return $ipAddress;
    }
}
