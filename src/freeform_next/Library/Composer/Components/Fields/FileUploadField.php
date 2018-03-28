<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2018, Solspace, Inc.
 * @link          https://solspace.com/expressionengine/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Addons\FreeformNext\Library\Composer\Components\Fields;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\FileUploadInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\FileUploadTrait;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\Traits\MultipleValueTrait;
use Solspace\Addons\FreeformNext\Library\Exceptions\FieldExceptions\FileUploadException;

class FileUploadField extends AbstractField implements MultipleValueInterface, FileUploadInterface
{
    const DEFAULT_MAX_FILESIZE_KB = 2048;
    const DEFAULT_FILE_COUNT      = 1;

    use MultipleValueTrait;
    use FileUploadTrait;

    /** @var array */
    protected $fileKinds;

    /** @var int */
    protected $maxFileSizeKB;

    /** @var int */
    protected $fileCount;

    /**
     * Cache for handles meant for preventing duplicate file uploads when calling ::validate() and ::uploadFile()
     * Stores the assetID once as value for handle key
     *
     * @var array
     */
    private static $filesUploaded = [];

    /**
     * Contains any errors for a given upload field
     *
     * @var array
     */
    private static $filesUploadedErrors = [];

    /**
     * Return the field TYPE
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_FILE;
    }

    /**
     * @return array
     */
    public function getFileKinds()
    {
        return $this->fileKinds;
    }

    /**
     * @return int
     */
    public function getMaxFileSizeKB()
    {
        return $this->maxFileSizeKB ?: self::DEFAULT_MAX_FILESIZE_KB;
    }

    /**
     * @return int
     */
    public function getFileCount()
    {
        $fileCount = (int) $this->fileCount;

        return $fileCount < 1 ? 1 : $fileCount;
    }

    /**
     * @return string
     */
    public function getInputHtml()
    {
        $attributes = $this->getCustomAttributes();

        return '<input '
            . $this->getAttributeString('name', $this->getHandle() . '[]')
            . $this->getAttributeString('type', $this->getType())
            . $this->getAttributeString('id', $this->getIdAttribute())
            . $this->getAttributeString('class', $attributes->getClass())
            . $this->getParameterString('multiple', $this->getFileCount() > 1)
            . $this->getRequiredAttribute()
            . $attributes->getInputAttributesAsString()
            . '/>';
    }

    /**
     * Validate the field and add error messages if any
     *
     * @return array
     */
    protected function validate()
    {
        $uploadErrors = [];

        if (!array_key_exists($this->handle, self::$filesUploaded)) {
            $exists = isset($_FILES[$this->handle]) && !empty($_FILES[$this->handle]['name']);
            if ($exists && $_FILES[$this->handle]['name'][0]) {
                $fileCount = count($_FILES[$this->handle]['name']);

                if ($fileCount > $this->getFileCount()) {
                    $uploadErrors[] = $this->translate(
                        'Tried uploading {count} files. Maximum {max} files allowed.',
                        ['max' => $this->getFileCount(), 'count' => $fileCount]
                    );
                }

                foreach ($_FILES[$this->handle]['name'] as $index => $name) {
                    $extension       = pathinfo($name, PATHINFO_EXTENSION);
                    $validExtensions = $this->getValidExtensions();

                    if (empty($_FILES[$this->handle]['tmp_name'][$index])) {
                        $errorCode = $_FILES[$this->handle]['error'][$index];

                        switch ($errorCode) {
                            case UPLOAD_ERR_INI_SIZE:
                            case UPLOAD_ERR_FORM_SIZE:
                                $uploadErrors[] = $this->translate('File size too large');
                                break;

                            case UPLOAD_ERR_PARTIAL:
                                $uploadErrors[] = $this->translate('The file was only partially uploaded');
                                break;
                        }
                        $uploadErrors[] = $this->translate('Could not upload file');
                    }

                    // Check for the correct file extension
                    if (!in_array(strtolower($extension), $validExtensions)) {
                        $uploadErrors[] = $this->translate(
                            '"{extension}" is not an allowed file extension',
                            ['extension' => $extension]
                        );
                    }

                    $fileSizeKB = ceil($_FILES[$this->handle]['size'][$index] / 1024);
                    if ($fileSizeKB > $this->getMaxFileSizeKB()) {
                        $uploadErrors[] = $this->translate(
                            'You tried uploading {fileSize}KB, but the maximum file upload size is {maxFileSize}KB',
                            ['fileSize' => $fileSizeKB, 'maxFileSize' => $this->getMaxFileSizeKB()]
                        );
                    }
                }
            } else if ($this->isRequired()) {
                $uploadErrors[] = $this->translate('This field is required');
            }

            // if there are errors - prevent the file from being uploaded
            if ($uploadErrors) {
                self::$filesUploaded[$this->handle] = null;
            }

            self::$filesUploadedErrors[$this->handle] = $uploadErrors;
        }

        return self::$filesUploadedErrors[$this->handle];
    }

    /**
     * Attempt to upload the file to its respective location
     *
     * @return int[] - asset ID
     * @throws FileUploadException
     */
    public function uploadFile()
    {
        if (isset($_FILES[$this->handle]) && empty($_FILES[$this->handle]['name']) && !$this->isRequired()) {
            return null;
        }

        if (!array_key_exists($this->handle, self::$filesUploaded)) {
            if (!isset($_FILES[$this->handle]) && !$this->required) {
                return null;
            }

            $response = $this->getForm()->getFileUploadHandler()->uploadFile($this);

            self::$filesUploaded[$this->handle]       = null;
            self::$filesUploadedErrors[$this->handle] = null;

            if ($response) {
                $errors         = $this->getErrors() ?: [];
                $responseErrors = $response->getErrors();

                if ($response->getAssetIds() || empty($responseErrors)) {
                    $this->values                       = $response->getAssetIds();
                    self::$filesUploaded[$this->handle] = $response->getAssetIds();

                    return $this->values;
                }

                if ($responseErrors) {
                    $this->errors = array_merge($errors, $responseErrors);
                    
                    self::$filesUploadedErrors[$this->handle] = $this->errors;
                    throw new FileUploadException(implode('. ', $responseErrors));
                }

                $this->errors = array_merge($errors, $responseErrors);

                self::$filesUploadedErrors[$this->handle] = $this->errors;
                throw new FileUploadException($this->translate('Could not upload file'));
            }

            return null;
        }

        if (!empty(self::$filesUploadedErrors[$this->handle])) {
            $this->errors = self::$filesUploadedErrors[$this->handle];
        }

        return self::$filesUploaded[$this->handle];
    }

    /**
     * Returns an array of all valid file extensions for this field
     *
     * @return array
     */
    private function getValidExtensions()
    {
        $allFileKinds = $this->getForm()->getFileUploadHandler()->getFileKinds();

        $selectedFileKinds = $this->getFileKinds();

        $allowedExtensions = [];
        if ($selectedFileKinds) {
            foreach ($selectedFileKinds as $kind) {
                if (array_key_exists($kind, $allFileKinds)) {
                    $allowedExtensions = array_merge($allowedExtensions, $allFileKinds[$kind]['extensions']);
                }
            }
        } else {
            foreach ($allFileKinds as $kind => $extensions) {
                $allowedExtensions = array_merge($allowedExtensions, $extensions['extensions']);
            }
        }

        return $allowedExtensions;
    }
}
