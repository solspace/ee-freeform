<?php

namespace Solspace\Addons\FreeformNext\Library\Connections;

use Solspace\Addons\FreeformNext\Library\DataObjects\ConnectionResult;

interface ConnectionInterface
{
    /**
     * @param array $keyValuePairs
     *
     * @return ConnectionResult
     */
    public function validate(array $keyValuePairs): ConnectionResult;

    /**
     * @param array $keyValuePairs
     *
     * @return ConnectionResult
     */
    public function connect(array $keyValuePairs): ConnectionResult;

    /**
     * @return array
     */
    public function getMapping(): array;
}
