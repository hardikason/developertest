<?php
/**
 * Copyright © Magento All rights reserved.
 * See COPYING.txt for license details.
 * @author Sonali Kosrabe <sonalikosrabe@outlook.com>
 */

namespace SK\DeveloperTest\Model\Config\Source;

use Magento\Directory\Model\AllowedCountries;
use Magento\Directory\Api\CountryInformationAcquirerInterface;

/**
 * Class CountriesDropdown
 * To get countries dropdown
 */
class CountriesDropdown extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var AllowedCountries
     */
    protected $allowedCountries;
    /**
     * @var CountryInformationAcquirerInterface
     */
    protected $countryInformationAcquirerInterface;

    /**
     * @param AllowedCountries $allowedCountries
     * @param CountryInformationAcquirerInterface $countryInformationAcquirerInterface
     */
    public function __construct(
        AllowedCountries $allowedCountries,
        CountryInformationAcquirerInterface $countryInformationAcquirerInterface
    ) {
        $this->allowedCountries = $allowedCountries;
        $this->countryInformationAcquirerInterface = $countryInformationAcquirerInterface;
    }

    /**
     * To get countries dropdown options list
     *
     * @return array|array[]|null
     */
    public function getAllOptions()
    {
        $allowedCountries = $this->allowedCountries->getAllowedCountries();
        $this->_options[] = ['label'=>'Select Options', 'value'=>''];
        foreach ($allowedCountries as $allowedCountry) {
            $countryInfo = $this->countryInformationAcquirerInterface->getCountryInfo($allowedCountry);
            $this->_options[] = ['label'=>$countryInfo->getFullNameLocale(), 'value'=> $allowedCountry];
        }
        return $this->_options;
    }
}
