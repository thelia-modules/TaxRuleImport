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
interface TaxRuleImportManagerInterface
{
    public function getResourcesPath();

    public function getSchemaValidatorFilePath();

    public function getXMLNamespace();
}