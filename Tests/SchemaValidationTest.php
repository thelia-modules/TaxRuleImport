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


/**
 * Class SchemaValidationTest
 * @package TaxRuleImport\Tests
 * @author Benjamin Perche <benjamin@thelia.net>
 */
class SchemaValidationTest extends \PHPUnit_Framework_TestCase
{
    public function testValidateValidSchema()
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->load(__DIR__.DS."..".DS."Resources".DS."example".DS."france.xml");
        $this->validate($dom);
    }

    public function testAllowEmptyFile()
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->load(__DIR__.DS."fixtures".DS."empty-tax-rules.xml");
        $this->validate($dom);
    }

    /**
     * @expectedException \Symfony\Component\Debug\Exception\ContextErrorException
     * @expectedExceptionMessage Warning: DOMDocument::schemaValidate(): Element 'tax-rule': Missing child element(s). Expected is ( countries ).
     */
    public function testCountriesTagIsMandatory()
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->load(__DIR__.DS."fixtures".DS."missing-countries.xml");
        $this->dryRun($dom);
    }

    /**
     * @expectedException \Symfony\Component\Debug\Exception\ContextErrorException
     * @expectedExceptionMessage Warning: DOMDocument::schemaValidate(): Element 'countries': Missing child element(s). Expected is ( country ).
     */
    public function testCountryTagIsMandatory()
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->load(__DIR__.DS."fixtures".DS."missing-country.xml");
        $this->dryRun($dom);
    }

    /**
     * @expectedException \Symfony\Component\Debug\Exception\ContextErrorException
     * @expectedExceptionMessage Warning: DOMDocument::schemaValidate(): Element 'tax-rule': Missing child element(s). Expected is ( descriptives ).
     */
    public function testDescriptivesTagIsMandatory()
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->load(__DIR__.DS."fixtures".DS."missing-descriptives.xml");
        $this->dryRun($dom);
    }

    /**
     * @expectedException \Symfony\Component\Debug\Exception\ContextErrorException
     * @expectedExceptionMessage Warning: DOMDocument::schemaValidate(): Element 'descriptives': Missing child element(s). Expected is ( descriptive ).
     */
    public function testDescriptiveTagIsMandatory()
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->load(__DIR__.DS."fixtures".DS."missing-descriptive.xml");
        $this->dryRun($dom);
    }

    /**
     * @expectedException \Symfony\Component\Debug\Exception\ContextErrorException
     * @expectedExceptionMessage Warning: DOMDocument::schemaValidate(): Element 'descriptive': The attribute 'locale' is required but missing.
     */
    public function testDescriptiveTagLocaleAttributeIsMandatory()
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->load(__DIR__.DS."fixtures".DS."missing-descriptive-locale.xml");
        $this->dryRun($dom);
    }

    public function testTaxesTagIsNotMandatory()
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->load(__DIR__.DS."fixtures".DS."missing-taxes.xml");
        $this->validate($dom);
    }

    public function testTaxTagIsNotMandatory()
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->load(__DIR__.DS."fixtures".DS."missing-tax.xml");
        $this->validate($dom);
    }

    public function testTaxRequirementIsNotMandatory()
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->load(__DIR__.DS."fixtures".DS."tax-without-requirement.xml");
        $this->validate($dom);
    }

    protected function validate(\DOMDocument $dom)
    {
        $this->assertTrue(
            $dom->schemaValidate(__DIR__.DS."..".DS."Resources".DS."xsd".DS."taxrule.xsd")
        );
    }

    protected function dryRun(\DOMDocument $dom)
    {
        $dom->schemaValidate(__DIR__.DS."..".DS."Resources".DS."xsd".DS."taxrule.xsd");
    }
}
