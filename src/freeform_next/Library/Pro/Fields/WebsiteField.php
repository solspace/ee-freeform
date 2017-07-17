<?php

namespace Solspace\Addons\FreeformNext\Library\Pro\Fields;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\TextField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Validation\Constraints\WebsiteConstraint;

class WebsiteField extends TextField
{
    /**
     * @inheritDoc
     */
    public function getConstraints()
    {
        return [
            new WebsiteConstraint($this->translate('Website not valid')),
        ];
    }
}
