<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use EllisLab\ExpressionEngine\Library\CP\Table;
use Solspace\Addons\FreeformNext\Library\Codepack\Codepack;
use Solspace\Addons\FreeformNext\Library\Codepack\Exceptions\CodepackException;
use Solspace\Addons\FreeformNext\Library\Codepack\Exceptions\FileObject\FileObjectException;
use Solspace\Addons\FreeformNext\Library\Codepack\Exceptions\Manifest\ManifestNotPresentException;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;

class DemoTemplatesController extends Controller
{
    public const FLASH_VAR_KEY = 'codepack_prefix';
    private const DEFAULT_GROUP = 'freeform-demo';

    /**
     * Show CodePack contents
     * Provide means to prefix the CodePack
     */
    public function index(): RedirectView|CpView
    {
        $codepack = $this->getCodepack();

        if (isset($_POST['prefix'])) {
            return $this->install();
        }

        $postInstallPrefix = ee()->session->flashdata(self::FLASH_VAR_KEY);
        if ($postInstallPrefix) {
            $view = new CpView(
                'codepack/post_install',
                [
                    'cp_page_title' => 'Demo Templates',
                    'table'         => $this->getPostInstallTable($codepack, $postInstallPrefix),
                ]
            );

            $view
                ->setHeading('Demo Templates')
                ->addBreadcrumb(new NavigationLink('Settings', 'settings/general'));

            return $view;
        }

        ob_start();
        include(PATH_THIRD . 'freeform_next/Templates/codepack/listing.php');
        $content = ob_get_clean();

        $view = new CpView(
            'codepack/index',
            [
                'cp_page_title'         => 'Demo Templates',
                'base_url'              => $this->getLink('settings/demo_templates'),
                'save_btn_text'         => 'Install',
                'save_btn_text_working' => 'Installing...',
                'form_url'              => $this->getLink('settings/demo_templates/install'),
                'codepack'              => $codepack,
                'prefix'                => $postInstallPrefix ?: self::DEFAULT_GROUP,
                'sections'              => [
                    [
                        [
                            'title'  => 'Template Group Name',
                            'desc'   => 'Choose a unique ExpressionEngine template group name for the demo. Installing again into the same group updates templates with matching names.',
                            'fields' => [
                                'prefix' => [
                                    'type'     => 'text',
                                    'value'    => $postInstallPrefix ?: self::DEFAULT_GROUP,
                                    'required' => true,
                                ],
                            ],
                        ],
                        [
                            'title'  => 'Explore the Demo',
                            'desc'   => 'The installed demo includes a home page, a form simulator, and a submissions area.',
                            'fields' => [
                                'test' => [
                                    'type'    => 'html',
                                    'content' => $content,
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $view
            ->setHeading('Demo Templates')
            ->addJavascript('code-pack')
            ->addBreadcrumb(new NavigationLink('Settings', 'settings/general'));

        return $view;
    }

    /**
     * Perform the install feats
     *
     * @return View
     * @throws CodepackException
     */
    private function install(): RedirectView|CpView
    {
        $codepack = $this->getCodepack();
        $prefix   = ee()->input->post('prefix');

        // Keep URL-safe group names, including dashes, but discard path separators.
        $prefix = trim(preg_replace('/[^a-zA-Z_0-9-]/', '', (string) $prefix), '-');

        if (empty($prefix)) {
            return new RedirectView($this->getLink('settings/demo_templates/'));
        }

        try {
            $codepack->install($prefix);

            ee()->session->set_flashdata(self::FLASH_VAR_KEY, $prefix);

            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asSuccess()
                ->withTitle(lang('Success'))
                ->defer();

            return new RedirectView($this->getLink('settings/demo_templates/'));
        } catch (FileObjectException $exception) {
            return new CpView(
                'codepack/no_templates',
                [
                    'codepack'         => $codepack,
                    'prefix'           => $prefix,
                    'exceptionMessage' => $exception->getMessage(),
                ]
            );
        }
    }

    /**
     * @return CodePack
     * @throws ManifestNotPresentException
     */
    private function getCodepack(): Codepack
    {
        return new Codepack(__DIR__ . '/../codepack');
    }

    /**
     * @param string   $prefix
     * @return array
     */
    private function getPostInstallTable(Codepack $codepack, $prefix)
    {

        /** @var Table $table */
        $table = ee('CP/Table', ['sortable' => false, 'searchable' => false]);

        $table->setColumns(
            [
                'Status'      => ['type' => Table::COL_TEXT],
                'Description' => ['type' => Table::COL_TEXT],
                'Details'     => ['type' => Table::COL_TEXT],
            ]
        );

        $tableData = [
            [
                'Success',
                'Templates were added',
                sprintf(
                    '%d templates were successfully added to your site as part of these Demo Templates.',
                    $codepack->getTemplates()->getContents()->getFileCount()
                ),
            ],
            [
                'Success',
                'Home Page',
                [
                    'content' => 'View the home page for these Demo Templates.',
                    'href'    => ee()->functions->create_url($prefix),
                ],
            ],
        ];

        $table->setData($tableData);

        return $table->viewData();
    }
}
