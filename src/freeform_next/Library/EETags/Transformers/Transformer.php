<?php

namespace Solspace\Addons\FreeformNext\Library\EETags\Transformers;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;

interface Transformer
{
    /**
     * Returns an array meant for EE Tag processing
     *
     * @param Form $form
     * @param int  $submissionCount
     *
     * @return array
     */
    public function transformForm(Form $form, $submissionCount = 0);
}
