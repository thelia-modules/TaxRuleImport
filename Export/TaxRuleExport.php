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

namespace TaxRuleImport\Export;

use TaxRuleImport\Tax\KnownTypes;
use Thelia\ImportExport\Export\ExportHandler;
use Thelia\Model\CountryQuery;
use Thelia\Model\Lang;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\FileFormat\FormatType;
use TaxRuleImport\Formatter\TaxRuleXmlFormatter;
use Thelia\Model\Map\CountryTableMap;
use Thelia\Model\TaxI18nQuery;
use Thelia\Model\TaxQuery;
use Thelia\Model\TaxRuleI18nQuery;
use Thelia\Model\TaxRuleQuery;

/**
 * Class TaxRuleExport
 * @package TaxRuleImport\Export
 * @author Benjamin Perche <benjamin@thelia.net>
 */
class TaxRuleExport extends ExportHandler
{
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
            TaxRuleXmlFormatter::TYPE,
        );
    }

    /**
     * @param  Lang                         $lang
     * @return ModelCriteria|array|BaseLoop
     *
     * Can be optimized
     */
    public function buildDataSet(Lang $lang)
    {
        /** @var TaxRuleQuery $query */
        $query = TaxRuleQuery::create();
        $this->filterQuery($query);

        $data = [];

        /** @var \Thelia\Model\TaxRule $taxRule */
        foreach ($query->find() as $taxRule) {
            $row = &$data[];

            $row["countries"] = CountryQuery::create()
                ->useTaxRuleCountryQuery()
                    ->filterByTaxRule($taxRule)
                ->endUse()
                ->select([CountryTableMap::ISOALPHA3])
                ->distinct()
                ->find()
                ->toArray()
            ;

            $row["i18n"] = $this->formatI18n(TaxRuleI18nQuery::create()->filterByTaxRule($taxRule));

            $taxes = TaxQuery::create()
                ->useTaxRuleCountryQuery()
                    ->filterByTaxRule($taxRule)
                ->endUse()
                ->find();

            $row["taxes"] = array();

            /** @var \Thelia\Model\Tax $tax */
            foreach ($taxes as $tax) {
                $taxRow = &$row["taxes"][];

                $taxRow["i18n"] = $this->formatI18n(TaxI18nQuery::create()->filterByTax($tax));
                $taxRow["type"] = KnownTypes::reverseResolve($tax->getType());
                $taxRow["requirements"] = $tax->getRequirements();
            }
        }

        return $data;
    }

    protected function formatI18n(ModelCriteria $query)
    {
        return $query
            ->select(["locale", "title", "description"])
            ->find()
            ->toArray()
        ;
    }

    protected function filterQuery(TaxRuleQuery $query)
    {
        $taxRuleId = $this->getRequest()->request->get("tax_rule_id");

        if (!empty($taxRuleId)) {
            $query->filterById($taxRuleId);
        }
    }
}
