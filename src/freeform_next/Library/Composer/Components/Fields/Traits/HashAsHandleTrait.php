<?php

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits;

trait HashAsHandleTrait
{
    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->getHash();
    }
}
