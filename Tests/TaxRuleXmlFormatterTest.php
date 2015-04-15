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

namespace TaxRuleImport\Tests;
use TaxRuleImport\Formatter\TaxRuleXmlFormatter;
use TaxRuleImport\Manager\TaxRuleImportManager;
use Thelia\Core\FileFormat\Formatting\FormatterData;
use Thelia\Core\Translation\Translator;
use Symfony\Component\DependencyInjection\Container;


/**
 * Class TaxRuleXmlFormatterTest
 * @package TaxRuleImport\Tests
 * @author Benjamin Perche <benjamin@thelia.net>
 */
class TaxRuleXmlFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaxRuleXmlFormatter
     */
    protected $formatter;

    protected function setUp()
    {
        $this->formatter = new TaxRuleXmlFormatter(new TaxRuleImportManager());
        new Translator(new Container());
    }

    /**
     * @dataProvider generateData
     */
    public function testEncodeDecodeDoesntLoseData(array $data)
    {
        $formatterData = (new FormatterData())->setData($data);

        $xml = $this->formatter->encode($formatterData);
        $outputData = $this->formatter->decode($xml)->getData();

        $this->assertEquals($data, $outputData);
    }

    public function generateData()
    {
        return [
            [
                [
                    [
                        "countries" => ["FRA", "USA"],
                        "i18n" => [
                            ["locale" => "fr_FR", "title" => "TVA 10%", "description" => "J'aime les taxes"],
                            ["locale" => "en_US", "title" => "French VAT 10%"],
                        ],
                        "taxes" => [
                            [
                                "type" => 'foo',
                                "i18n" => [
                                    ["locale" => "fr_FR", "title" => "10% en plus", "description" => "Cadeau"],
                                    ["locale" => "en_US", "title" => "+ 10%"],
                                ],
                                "requirements" => [
                                    "percent" => 10
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
