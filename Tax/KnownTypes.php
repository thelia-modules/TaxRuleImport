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

namespace TaxRuleImport\Tax;


/**
 * Class KnownTypes
 * @package TaxRuleImport\Tax
 * @author Benjamin Perche <benjamin@thelia.net>
 */
class KnownTypes
{
    private static $knownTaxTypes = array(
        "amount" => 'Thelia\TaxEngine\TaxType\FixAmountTaxType',
        "percent" => 'Thelia\TaxEngine\TaxType\PricePercentTaxType',
        "feature_percent" => 'Thelia\TaxEngine\TaxType\FeatureFixAmountTaxType',
    );

    public static function resolve($type, $default = null)
    {
        if(static::has($type)) {
            return static::$knownTaxTypes[$type];
        }

        return $default;
    }

    public static function reverseResolve($class, $default = null)
    {
        if (false !== $key = array_search($class, static::$knownTaxTypes)) {
            return $key;
        }

        return $default;
    }

    public static function has($type)
    {
        return isset(static::$knownTaxTypes[$type]);
    }
}
