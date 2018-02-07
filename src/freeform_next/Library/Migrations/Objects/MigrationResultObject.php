<?php

namespace Solspace\Addons\FreeformNext\Library\Migrations\Objects;

class MigrationResultObject
{
    public $success = false;
    public $finished = false;
    public $errors = [];
    public $submissionsInfo = [];

    public function addError($message)
    {
        $this->errors[] = $message;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        if ($this->errors) {
            return true;
        }

        return false;
    }

    public function isMigrationSuccessful()
    {
        return $this->success;
    }
}