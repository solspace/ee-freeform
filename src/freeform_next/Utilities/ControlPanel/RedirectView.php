<?php

namespace Solspace\Addons\FreeformNext\Utilities\ControlPanel;

class RedirectView extends View
{
    /** @var string */
    private $url;

    /**
     * RedirectView constructor.
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    public function compile()
    {
        header('Location: ' . $this->url);
        die();
    }

}
