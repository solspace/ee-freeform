<?php

namespace Solspace\Addons\FreeformNext\Utilities;

class Plugin
{
    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        $param = ee()->TMPL->fetch_param($name);

        if (!$param) {
            return $default;
        }

        return $param;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getPost($name, $default = null)
    {
        $value = ee()->input->post($name);

        if (!$value) {
            return $default;
        }

        return $value;
    }

    /**
     * @param mixed $object
     */
    public function returnJson($object)
    {
        echo json_encode($object);
        die();
    }

    /**
     * Redirects to a given $url
     *
     * @param string $url
     */
    public function redirect($url)
    {
        header('Location: ' . $url);
        die();
    }
}
