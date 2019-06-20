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

class ClassicFormNotificationsHelper extends AddonBuilder
{
    public function getClassicFormNotifications()
    {
        $notifications	= $this->model('notification')->get();

        return $notifications;
    }
}
