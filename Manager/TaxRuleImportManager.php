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

namespace TaxRuleImport\Manager;

/**
 * Class TaxRuleImportManager
 * @package TaxRuleImport\Manager
 * @author Benjamin Perche <benjamin@thelia.net>
 */
class TaxRuleImportManager implements TaxRuleImportManagerInterface
{
    public function getResourcesPath()
    {
        return realpath(__DIR__.DS."..".DS."Resources").DS;
    }

    public function getSchemaValidatorFilePath()
    {
        return realpath($this->getResourcesPath()."xsd".DS."taxrule.xsd");
    }

    public function getXMLNamespace()
    {
        return "http://thelia.net/schema/dic/tax-rule";
    }
}
