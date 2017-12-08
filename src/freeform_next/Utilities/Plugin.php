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

    /**
     * Returns a path to the third-party theme folder
     *
     * @return string
     */
    public function theme_folder_url()
    {
        return rtrim(URL_THIRD_THEMES, '/') . '/' . $this->getAddonLowerName() . '/';
    }

    /**
     * @return string
     */
    public function returnNoResults()
    {
        $pattern = '/{if (?:freeform_next|submission|form):no_results}(.*?){\/if}/s';

        if (preg_match($pattern, ee()->TMPL->tagdata, $match)) {
            return $match[1];
        }

        return ee()->TMPL->no_results();
    }

    /**
     * @return string
     */
    private function getAddonLowerName()
    {
        return AddonInfo::getInstance()->getLowerName();
    }

    /**
     * @return bool
     */
    protected function loadLanguageFiles()
    {
        $lowerName       = $this->getAddonLowerName();
        $language        = ee()->lang;
        $loadedLanguages = $language->is_loaded;

        if (
            //install wizrd doesn't set EE lang
            empty($loadedLanguages) ||
            (
                !in_array("{$lowerName}_lang.php", $loadedLanguages, true) &&
                !in_array("lang.$lowerName.php", $loadedLanguages, true)
            )
        ) {
            if (
                $language &&
                is_object($language) &&
                isset(ee()->session, ee()->session->userdata['language'])
            ) {
                $language->loadfile($lowerName);

                return true;
            }
        }

        $object = &ee()->session;

        if (
            is_object($object) &&
            strtolower(get_class($object)) === 'session' &&
            $object->userdata['language']
        ) {
            $userLanguage = $object->userdata['language'];
        } else {
            $userLanguage = 'english';
            if (ee()->input->cookie('language')) {
                $userLanguage = ee()->input->cookie('language');
            } else if (ee()->config->item('deft_lang')) {
                $userLanguage = ee()->config->item('deft_lang');
            }
        }

        //no BS
        $userLanguage = ee()->security->sanitize_filename($userLanguage);
        $addonPath    = __DIR__ . '/../';

        if (!in_array($lowerName, $loadedLanguages, true)) {
            $options = [
                $addonPath . 'language/' . $userLanguage . '/lang.' . $lowerName . '.php',
                $addonPath . 'language/' . $userLanguage . '/' . $lowerName . '_lang.php',
                $addonPath . 'language/english/lang.' . $lowerName . '.php',
                $addonPath . 'language/english/' . $lowerName . '_lang.php',
            ];

            $success = false;

            $lang = [];
            foreach ($options as $path) {
                if (file_exists($path) && include $path) {
                    $success = true;
                    break;
                }
            }

            if (!$success) {
                return false;
            }

            if (!empty($lang)) {
                ee()->lang->language = array_merge(
                    ee()->lang->language,
                    $lang
                );
            }
        }

        return true;
    }
}
