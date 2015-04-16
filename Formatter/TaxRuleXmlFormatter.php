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

use Symfony\Component\DependencyInjection\SimpleXMLElement;
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
    const TYPE = "tax-rules-xml";

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
        $xml = new SimpleXMLElement("<tax-rules></tax-rules>");

        foreach ($data->getDataReverseAliases() as $row) {
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

                foreach ($rawTax["requirements"] as $name => $requirementValue) {
                    $requirement = $tax->addChild("requirement", $requirementValue);
                    $requirement->addAttribute("key", $name);
                }
            }
        }

        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->loadXML($xml->saveXML());
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = true;

        return $dom->C14N();
    }

    protected function extractDescriptive($row, \SimpleXMLElement $parent)
    {
        $descriptives = $parent->addChild("descriptives");

        foreach ($row as $values) {
            $descriptive = $descriptives->addChild("descriptive");
            $descriptive->addAttribute("locale", $values["locale"]);

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

        $xml = new SimpleXMLElement($rawData);
        $xml->registerXPathNamespace("tax-rules", $this->manager->getXMLNamespace());

        $data = array();

        /** @var SimpleXMLElement $taxRule */
        foreach ($xml->xpath("//tax-rule") as $taxRule) {
            $row = &$data[];

            foreach ($taxRule->xpath("./countries/country") as $country) {
                $row["countries"][] = (string) $country;
            }

            $this->parseDescriptives($taxRule, $row);

            /** @var SimpleXMLElement $tax */
            foreach ($taxRule->xpath("./taxes/tax") as $tax) {
                $taxRow = &$row["taxes"][];
                $taxType = $tax->getAttributeAsPhp("type");

                $taxRow["type"] = $taxType;

                $this->parseDescriptives($tax, $taxRow);

                $taxRow["requirements"] = [];

                /** @var SimpleXMLElement $requirement */
                foreach ($tax->xpath("./requirement") as $requirement) {
                    $key = $requirement->getAttributeAsPhp("key");
                    $taxRow["requirements"][$key] = (string) $requirement;
                }
            }
        }

        return (new FormatterData())->setData($data);
    }

    protected function parseDescriptives(SimpleXMLElement $xml, array &$row)
    {
        /** @var SimpleXMLElement $descriptive */
        foreach ($xml->xpath("./descriptives/descriptive") as $descriptive) {
            $locale = $descriptive->getAttributeAsPhp("locale");
            $title = (string) $descriptive->title;
            $description = (string) $descriptive->description;

            $row["i18n"][] = [
                "locale" => $locale,
                "title" => $title,
                "description" => $description,
            ];
        }
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
