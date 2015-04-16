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

namespace TaxRuleImport\Hook;

use TaxRuleImport\TaxRuleImport;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\ExportQuery;
use Thelia\Model\Map\ExportTableMap;

/**
 * Class ExportFilterHook
 * @package TaxRuleImport\Hook
 * @author Benjamin Perche <benjamin@thelia.net>
 */
class ExportFilterHook extends BaseHook
{
    public function onExportBottom(HookRenderEvent $event)
    {
        $export = ExportQuery::create()
            ->filterByRef(TaxRuleImport::EXPORT_REF)
            ->select([ExportTableMap::ID])
            ->limit(1)
            ->find()
            ->toArray()
        ;

        if (count($export) && $export[0] == $event->getArgument("id")) {
            $event->add($this->render("tax-rule-import/filters.html"));
        }
    }
}
