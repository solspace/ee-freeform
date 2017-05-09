<?php

namespace Solspace\Addons\FreeformNext\Library\EETags;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\DataObjects\SubmissionAttributes;
use Solspace\Addons\FreeformNext\Library\EETags\Transformers\FormTransformer;
use Solspace\Addons\FreeformNext\Library\EETags\Transformers\SubmissionTransformer;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;
use Solspace\Addons\FreeformNext\Repositories\SubmissionRepository;

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
     * @param SubmissionAttributes $attributes
     *
     * @return string
     */
    public function getOutput(SubmissionAttributes $attributes)
    {
        $output = $this->content;
        $output = ee()->TMPL->parse_variables($output, $this->transform($attributes));

        return $output;
    }

    /**
     * @param SubmissionAttributes $attributes
     *
     * @return array
     */
    private function transform(SubmissionAttributes $attributes)
    {
        $formTransformer = new FormTransformer();
        $absoluteSubmissionCount = SubmissionRepository::getInstance()->getAllSubmissionCountFor($attributes);

        $baseData = $formTransformer->transformForm($this->form, $absoluteSubmissionCount);

        $submissionTransformer = new SubmissionTransformer();

        $data = [];

        $count           = 1;
        $submissionCount = count($this->submissions);
        foreach ($this->submissions as $submissionModel) {
            $data[] = array_merge(
                $baseData,
                $submissionTransformer->transformSubmission(
                    $submissionModel,
                    $count++,
                    $submissionCount,
                    $absoluteSubmissionCount,
                    $attributes
                )
            );
        }

        return $data;
    }

}
