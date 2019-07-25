<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          http://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Migrations\Helpers;

use Solspace\Addons\Freeform\Library\AddonBuilder;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;

class ClassicFormStatusHelper extends AddonBuilder
{
    public function getClassicFormStatuses()
    {
        $available_statuses	= $this->model('preference')->get_form_statuses();

        return $available_statuses;
    }
}
