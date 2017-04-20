<?php

namespace Solspace\Addons\FreeformNext\Library\EETags;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\EETags\Transformers\FormTransformer;
use Solspace\Addons\FreeformNext\Library\EETags\Transformers\SubmissionTransformer;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;
use Solspace\Addons\FreeformNext\Repositories\FormRepository;

class SubmissionToTagDataTransformer
{
    const PATTERN_FIELD_RENDER           = '/{field:([a-zA-Z0-9\-_]+):(render(?:_?[a-zA-Z]+)?)?\s+([^}]+)}/i';
    const PATTERN_FIELD_RENDER_VARIABLES = '/\b([a-zA-Z0-9_\-:]+)=(?:\'|")([^"\']+)(?:\'|")/';

    /** @var Form */
    private $form;

    /** @var string */
    private $content;

    /** @var SubmissionModel[] */
    private $submissions;

    /**
     * FormToTagDataTransformer constructor.
     *
     * @param Form   $form
     * @param string $content
     * @param array  $submissions
     */
    public function __construct(Form $form, $content, array $submissions)
    {
        $this->form        = $form;
        $this->content     = $content;
        $this->submissions = $submissions;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        $output = $this->content;
        $output = ee()->TMPL->parse_variables($output, $this->transform());

        return $output;
    }

    /**
     * @return array
     */
    private function transform()
    {
        $formTransformer = new FormTransformer();

        $submissionCount = FormRepository::getInstance()->getFormSubmissionCount([$this->form->getId()]);
        if (!empty($submissionCount)) {
            $submissionCount = reset($submissionCount);
        } else {
            $submissionCount = 0;
        }

        $baseData = $formTransformer->transformForm($this->form, $submissionCount);

        $submissionTransformer = new SubmissionTransformer();
        $data = [];
        foreach ($this->submissions as $submissionModel) {
            $data[] = array_merge(
                $baseData,
                $submissionTransformer->transformSubmission($submissionModel)
            );
        }

        return $data;
    }

}
