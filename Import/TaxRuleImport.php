<?php
/*************************************************************************************/
/* This file is part of the Thelia package.                                          */
/*                                                                                   */
/* Copyright (c) OpenStudio                                                          */
/* email : dev@thelia.net                                                            */
/* web : http://www.thelia.net                                                       */
/*                                                                                   */
/* For the full copyright and license information, please view the LICENSE.txt       */
/* file that was distributed with this source code.                                  */
/*************************************************************************************/

namespace TaxRuleImport\Import;

use Propel\Runtime\Propel;
use TaxRuleImport\Tax\KnownTypes;
use Thelia\Core\FileFormat\FormatType;
use Thelia\ImportExport\Import\ImportHandler;
use Thelia\Core\FileFormat\Formatting\FormatterData;
use Thelia\Model\CountryQuery;
use Thelia\Model\Tax;
use Thelia\Model\TaxRule;
use Thelia\Model\TaxRuleCountry;

/**
 * Class TaxRuleImport
 * @package TaxRuleImport\Import
 * @author Benjamin Perche <benjamin@thelia.net>
 */
class TaxRuleImport extends ImportHandler
{
    protected static $countryCache = array();
    protected static $taxCache = array();

    /**
     * @return string|array
     *
     * Define all the type of formatters that this can handle
     * return a string if it handle a single type ( specific exports ),
     * or an array if multiple.
     *
     * Thelia types are defined in \Thelia\Core\FileFormat\FormatType
     *
     * example:
     * return array(
     *     FormatType::TABLE,
     *     FormatType::UNBOUNDED,
     * );
     */
    public function getHandledTypes()
    {
        return array(
            FormatType::UNBOUNDED,
        );
    }

    /**
     * @return array The mandatory columns to have for import
     */
    protected function getMandatoryColumns()
    {
        return [];
    }

    /**
     * @param \Thelia\Core\FileFormat\Formatting\FormatterData
     * @return string|array error messages
     *
     * The method does the import routine from a FormatterData
     */
    public function retrieveFromFormatterData(FormatterData $data)
    {
        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            foreach ($data->getData() as $row) {
                $taxRule = new TaxRule();
                $this->hydrateI18n($taxRule, $row);

                $taxRule->save($con);
                $countries = $this->getCountries($row["country"]);

                foreach ($row["taxes"] as $rawTax) {
                    $tax = new Tax();
                    $this->hydrateI18n($tax, $rawTax);

                    $tax
                        ->setType(KnownTypes::resolve($rawTax["type"]))
                        ->setRequirements($rawTax["requirements"])
                        ->save($con)
                    ;

                    foreach ($countries as $country) {
                        $taxRuleCountry = new TaxRuleCountry();
                        $taxRuleCountry
                            ->setTaxRule($taxRule)
                            ->setTax($tax)
                            ->setCountry($country)
                            ->save($country)
                        ;
                    }

                }
            }

            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();

            throw $e;
        }
    }

    protected function hydrateI18n($obj, array $row)
    {
        foreach ($row["i18n"] as $translation) {
            $obj->getTranslation($translation["locale"])
                ->setTitle($translation["title"])
                ->setDescription(isset($translation["description"]) ? $translation["description"] : null);
        }
    }

    protected function getCountries($countries)
    {
        $countriesObject = [];

        foreach ($countries as $country) {
            if (!isset(static::$countryCache[$country])) {
                $countryObject = CountryQuery::create()
                    ->filterByIsoalpha2($country)
                    ->_or()
                    ->filterByIsoalpha3($country)
                    ->_or()
                    ->filterByIsocode($country)
                    ->findOne();

                if (null === $countryObject) {
                    throw new \InvalidArgumentException(
                        sprintf("The country code '%s' doesn't belong to any known country isoalpha2, isoalpha3 or isocode", $country)
                    );
                }

                $countriesObject[] = static::$countryCache[$country] = $countryObject;
            }
        }

        return $countriesObject;
    }
}
