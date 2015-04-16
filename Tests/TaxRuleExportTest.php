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

use Symfony\Component\DependencyInjection\Container;
use TaxRuleImport\Export\TaxRuleExport;
use TaxRuleImport\Formatter\TaxRuleXmlFormatter;
use TaxRuleImport\Manager\TaxRuleImportManager;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\Model\LangQuery;

/**
 * Class TaxRuleExportTest
 * @package TaxRuleImport\Tests
 * @author Benjamin Perche <benjamin@thelia.net>
 */
class TaxRuleExportTest extends \PHPUnit_Framework_TestCase
{
    /** @var TaxRuleExport */
    protected $export;

    /** @var TaxRuleXmlFormatter */
    protected $formatter;

    protected function setUp()
    {
        $container = new Container();
        $request = new Request();

        $container->set("request", $request);

        new Translator($container);
        $this->export = new TaxRuleExport($container);
        $this->formatter = new TaxRuleXmlFormatter(new TaxRuleImportManager());
    }

    public function testExportMustBeCompatibleWithFormatting()
    {
        $builtData = $this->export->buildData(LangQuery::create()->findOneByLocale("fr_FR"));

        $formattedData = $this->formatter->encode($builtData);
        $this->assertInstanceOf("SimpleXmlElement", new \SimpleXmlElement($formattedData));
    }
}
