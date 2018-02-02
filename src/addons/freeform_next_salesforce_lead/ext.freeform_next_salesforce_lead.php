<?php
use Solspace\Addons\FreeformNext\Utilities\Extension\FreeformIntegrationExtension;

/**
 * Freeform for ExpressionEngine
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */
class Freeform_next_salesforce_lead_ext extends FreeformIntegrationExtension
{
    /**
     * @return array
     */
    public function registerIntegrations()
    {
        $existingIntegrations = ee()->extensions->last_call ?: [];

        return array_merge(
            $existingIntegrations,
            [
                'Solspace\Addons\FreeformNextSalesforceLead\Types\SalesforceLead' => 'Salesforce: Lead',
            ]
        );
    }
}
