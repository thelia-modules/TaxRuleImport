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

use Thelia\Core\FileFormat\Formatting\Exception\BadFormattedStringException;
use Thelia\Core\FileFormat\FormatType;
use Thelia\ImportExport\Import\ImportHandler;
use Thelia\Core\FileFormat\Formatting\FormatterData;
use Thelia\Model\CountryQuery;

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
        return ["country", "tax"];
    }

    /**
     * @param \Thelia\Core\FileFormat\Formatting\FormatterData
     * @return string|array error messages
     *
     * The method does the import routine from a FormatterData
     */
    public function retrieveFromFormatterData(FormatterData $data)
    {
        foreach ($data->getData() as $row) {
            $country = $this->getCountry($row["country"]);
            $taxes = [$this->getTax($row["tax"])];

            for ($i = 1; isset($row["tax".$i]); ++$i) {
                if (!empty($row["tax".$i])) {
                    $taxes[] = $this->getTax($row["tax" . $i]);
                }
            }
        }
    }

    protected function getCountry($country)
    {
        if (! isset(static::$countryCache[$country])) {
            $countryObject = CountryQuery::create()
                ->filterByIsoalpha2($country)
                    ->_or()
                ->filterByIsoalpha3($country)
                    ->_or()
                ->filterByIsocode($country)
                ->findOne()
            ;

            if (null === $countryObject) {
                throw new \InvalidArgumentException(
                    sprintf("The country code '%s' doesn't belong to any known country isoalpha2, isoalpha3 or isocode", $country)
                );
            }

            static::$countryCache[$country] = $countryObject;
        }

        return static::$countryCache[$country];
    }

    protected function getTax($rawTax)
    {
        if (! isset(static::$taxCache[$rawTax])) {

        }

        return static::$taxCache[$rawTax];
    }
}
