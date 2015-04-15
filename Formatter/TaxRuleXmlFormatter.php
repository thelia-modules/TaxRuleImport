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

namespace TaxRuleImport\Formatter;

use TaxRuleImport\Manager\TaxRuleImportManagerInterface;
use TaxRuleImport\Tax\KnownTypes;
use Thelia\Core\FileFormat\Formatting\AbstractFormatter;
use Thelia\Core\FileFormat\Formatting\FormatterData;

/**
 * Class TaxRuleXmlFormatter
 * @package TaxRuleImport\Formatter
 * @author Benjamin Perche <benjamin@thelia.net>
 */
class TaxRuleXmlFormatter extends AbstractFormatter
{
    const TYPE = "taxrule-xml";

    protected $manager;

    public function __construct(TaxRuleImportManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return string
     *
     * This method must return a string, the name of the format.
     *
     * example:
     * return "XML";
     */
    public function getName()
    {
        return "TaxRuleXML";
    }

    /**
     * @return string
     *
     * This method must return a string, the extension of the file format, without the ".".
     * The string should be lowercase.
     *
     * example:
     * return "xml";
     */
    public function getExtension()
    {
        return "xml";
    }

    /**
     * @return string
     *
     * This method must return a string, the mime type of the file format.
     *
     * example:
     * return "application/json";
     */
    public function getMimeType()
    {
        return "application/xml";
    }

    /**
     * @param  FormatterData $data
     * @return mixed
     *
     * This method must use a FormatterData object and output
     * a formatted value.
     */
    public function encode(FormatterData $data)
    {
        $xml = new \SimpleXMLElement("<tax-rules></tax-rules>");

        foreach ($data as $row) {
            $taxRule = $xml->addChild("tax-rule");

            $countries = $taxRule->addChild("countries");

            foreach ($row["countries"] as $country) {
                $countries->addChild("country", $country);
            }

            $this->extractDescriptive($row["i18n"], $taxRule);
            $taxes = $taxRule->addChild("taxes");

            foreach ($row["taxes"] as $rawTax) {
                $tax = $taxes->addChild("tax");
                $tax->addAttribute("type", KnownTypes::reverseResolve($rawTax["type"], $rawTax["type"]));

                $this->extractDescriptive($rawTax["i18n"], $tax);

                $requirements = json_decode($rawTax["requirements"]);
                foreach ($requirements as $name => $requirementValue) {
                    $requirement = $tax->addChild("requirement", $requirementValue);
                    $requirement->addAttribute("key", $name);
                }
            }
        }
    }

    protected function extractDescriptive($row, \SimpleXMLElement $parent)
    {
        $descriptives = $parent->addChild("descriptives");

        foreach ($row as $locale => $values) {
            $descriptive = $descriptives->addChild("descriptive");
            $descriptive->addAttribute("locale", $locale);

            $descriptive->addChild("title", $values["title"]);

            if (isset($values["description"])) {
                $descriptive->addChild("description", $values["description"]);
            }
        }
    }

    /**
     * @param $rawData
     * @return FormatterData
     *
     * This must takes raw data as argument and outputs
     * a FormatterData object.
     */
    public function decode($rawData)
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->loadXML($rawData);

        $dom->schemaValidate($this->manager->getSchemaValidatorFilePath());

        $xml = new \SimpleXMLElement($rawData);
        $xml->xpath("");

        $data = array();

        return (new FormatterData())->setData($data);
    }

    /**
     * @return string
     *
     * return a string that defines the handled format type.
     *
     * Thelia types are defined in \Thelia\Core\FileFormat\FormatType
     *
     * examples:
     *   return FormatType::TABLE;
     *   return FormatType::UNBOUNDED;
     */
    public function getHandledType()
    {
        return static::TYPE;
    }
}
