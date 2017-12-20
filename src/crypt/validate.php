<?php

$version = \Solspace\Addons\FreeformNext\Library\Helpers\FreeformHelper::get('version');

if ($version === FREEFORM_EXPRESS) {
    $item = $args[1];

    if ($item instanceof \Solspace\Addons\FreeformNext\Model\FormModel) {
        $count = (int) ee()->db
            ->select('COUNT(*) as total')
            ->get('freeform_next_forms')
            ->row()
            ->total;

        if (!$item->id && $count > 0) {
            throw new \Exception('Form limit reached');
        }
    }

    if ($item instanceof \Solspace\Addons\FreeformNext\Model\FieldModel) {
        $count = (int) ee()->db
            ->select('COUNT(*) as total')
            ->get('freeform_next_fields')
            ->row()
            ->total;

        if (!$item->id && $count >= 15) {
            throw new \Exception('Maximum limit of 15 fields reached.');
        }
    }

    if ($item instanceof \Solspace\Addons\FreeformNext\Model\StatusModel) {
        throw new \Exception('Cannot edit statuses in Freeform Express');
    }
}
