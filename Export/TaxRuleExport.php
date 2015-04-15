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
use Thelia\ImportExport\Export\ExportHandler;
use Thelia\Model\Lang;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\FileFormat\FormatType;
use TaxRuleImport\Formatter\TaxRuleXmlFormatter;


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
            FormatType::UNBOUNDED,
            TaxRuleXmlFormatter::TYPE,
        );
    }

    /**
     * @param  Lang $lang
     * @return ModelCriteria|array|BaseLoop
     */
    public function buildDataSet(Lang $lang)
    {

    }
}
