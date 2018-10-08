<?php

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Properties;

use Solspace\Addons\FreeformNext\Library\Connections\AbstractConnection;
use Solspace\Addons\FreeformNext\Library\Connections\ConnectionInterface;
use Solspace\Addons\FreeformNext\Library\Exceptions\Connections\ConnectionException;
use Solspace\Addons\FreeformNext\Library\Logging\EELogger;
use Solspace\Addons\FreeformNext\Library\Logging\LoggerInterface;

class ConnectionProperties extends AbstractProperties
{
    /** @var array */
    protected $list;

    /** @var array */
    private $compiledList;

    /**
     * @return ConnectionInterface[]
     */
    public function getList()
    {
        if (null === $this->compiledList) {
            $list = [];
            if ($this->list) {
                foreach ($this->list as $item) {
                    try {
                        $list[] = AbstractConnection::create($item);
                    } catch (ConnectionException $e) {
                        $logger = new EELogger();
                        $logger->log(LoggerInterface::LEVEL_WARNING, $e->getMessage());
                    }
                }
            }

            $this->compiledList = $list;
        }

        return $this->compiledList;
    }

    /**
     * @inheritDoc
     */
    protected function getPropertyManifest()
    {
        return [
            'list' => self::TYPE_ARRAY,
        ];
    }
}
