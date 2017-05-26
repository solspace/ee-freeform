<?php

namespace Solspace\Addons\FreeformNext\Library\DataObjects;

class PluginUpdate
{
    /** @var string */
    private $version;

    /** @var string */
    private $downloadUrl;

    /** @var \DateTime */
    private $date;

    /** @var array */
    private $bugfixes;

    /** @var array */
    private $features;

    /** @var array */
    private $notes;

    /**
     * PluginUpdate constructor.
     *
     * @param string    $version
     * @param string    $downloadUrl
     * @param \DateTime $date
     * @param array     $items
     */
    public function __construct($version, $downloadUrl, \DateTime $date, array $items)
    {
        $this->version     = $version;
        $this->downloadUrl = $downloadUrl;
        $this->date        = $date;

        $this->bugfixes = [];
        $this->features = [];
        $this->notes    = [];

        $this->parseItems($items);
    }

    /**
     * @param array $items
     */
    private function parseItems(array $items)
    {
        foreach ($items as $item) {
            if (preg_match('/\[(\w+)\]\s*(.*)/', $item, $matches)) {
                list ($match, $type, $string) = $matches;

                switch (strtolower($type)) {
                    case 'fixed':
                        $this->bugfixes[] = $string;
                        break;

                    case 'improved':
                        $this->notes[] = $string;
                        break;

                    case 'added':
                    default:
                        $this->features[] = $string;
                        break;
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getDownloadUrl()
    {
        return $this->downloadUrl;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return array
     */
    public function getBugfixes()
    {
        return $this->bugfixes;
    }

    /**
     * @return array
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @return array
     */
    public function getNotes()
    {
        return $this->notes;
    }
}
