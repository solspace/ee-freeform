<?php

namespace Solspace\Addons\FreeformNext\Library\Helpers;

class TwigHelper
{
    /**
     * @param string $output
     *
     * @return \Twig_Markup
     */
    public static function getRaw($output)
    {
        return new \Twig_Markup($output, 'UTF-8');
    }

    /**
     * @param string $templateName
     * @param array  $variables
     *
     * @return string
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     */
    public static function renderTemplate($templateName, array $variables)
    {
        return self::getTemplateEnvironment()->render($templateName, $variables);
    }

    /**
     * @param string $string
     * @param array  $variables
     *
     * @return string
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Loader
     */
    public static function renderString($string, array $variables = [])
    {
        $template = self::getArrayEnvironment()->createTemplate($string);

        return $template->render($variables);
    }

    /**
     * @return \Twig_Environment
     */
    private static function getArrayEnvironment()
    {
        static $env;

        if (null === $env) {
            $loader = new \Twig_Loader_Array([]);
            $env = new \Twig_Environment($loader);
        }

        return $env;
    }

    /**
     * @return \Twig_Environment
     */
    private static function getTemplateEnvironment()
    {
        static $env;

        if (null === $env) {
            $loader = new \Twig_Loader_Filesystem(
                [
                    __DIR__ . '/../../Templates/',
                ]
            );
            $env = new \Twig_Environment($loader);
        }

        return $env;
    }
}
