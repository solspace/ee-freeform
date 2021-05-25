<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2021, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v2/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\DataObjects;

use Solspace\Addons\FreeformNext\Library\Exceptions\DataObjects\EmailTemplateException;
use Solspace\Addons\FreeformNext\Library\Helpers\StringHelper;

class EmailTemplate
{
    const METADATA_PATTERN = "/{!--\s*__KEY__:\s*(.*)\s*--}/";

    /** @var string */
    private $name;

    /** @var string */
    private $fileName;

    /** @var string */
    private $handle;

    /** @var string */
    private $description;

    /** @var string */
    private $templateData;

    /** @var string */
    private $fromEmail;

    /** @var string */
    private $fromName;

    /** @var string */
    private $replyToEmail;

    /** @var bool */
    private $includeAttachments;

    /** @var string */
    private $subject;

    /** @var string */
    private $body;

    /**
     * EmailTemplate constructor.
     *
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $this->templateData = file_get_contents($filePath);

        $this->handle   = pathinfo($filePath, PATHINFO_FILENAME);
        $this->fileName = pathinfo($filePath, PATHINFO_BASENAME);

        $name = $this->getMetadata('templateName', false);
        if (!$name) {
            $name = StringHelper::camelize(StringHelper::humanize($this->handle));
        }

        $this->name = $name;

        $this->description  = $this->getMetadata('description', false);
        $this->fromEmail    = $this->getMetadata('fromEmail', true);
        $this->fromName     = $this->getMetadata('fromName', true);
        $this->replyToEmail = $this->getMetadata('replyToEmail', false);
        $this->subject      = $this->getMetadata('subject', true);
        $this->body         = preg_replace('/{!--.*--}\n?/', '', $this->templateData);

        $includeAttachments = $this->getMetadata('includeAttachments', false);
        $includeAttachments = $includeAttachments &&
            in_array(strtolower($includeAttachments), ['true', 'yes', 'y', '1'], true);

        $this->includeAttachments = $includeAttachments;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @return string
     */
    public function getReplyToEmail()
    {
        return $this->replyToEmail;
    }

    /**
     * @return bool
     */
    public function isIncludeAttachments()
    {
        return $this->includeAttachments;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $key
     * @param bool   $required
     *
     * @return null|string
     * @throws EmailTemplateException
     */
    private function getMetadata($key, $required = false)
    {
        $value   = null;
        $pattern = str_replace('__KEY__', $key, self::METADATA_PATTERN);

        if (preg_match($pattern, $this->templateData, $matches)) {
            list ($_, $value) = $matches;
            $value = trim($value);
        } else if ($required) {
            throw new EmailTemplateException(
                sprintf('Email template "%s" does not contain "%s"', $this->fileName, $key)
            );
        }

        return $value;
    }
}
