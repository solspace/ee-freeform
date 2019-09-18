<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2019, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v1/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Session;

use Solspace\Addons\FreeformNext\Library\Composer\Components\AbstractField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Attributes\DynamicNotificationAttributes;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\CheckboxField;
use Solspace\Addons\FreeformNext\Library\Composer\Components\Fields\SubmitField;
use Solspace\Addons\FreeformNext\Library\Helpers\HashHelper;

class FormValueContext implements \JsonSerializable
{
    const FORM_HASH_DELIMITER = '_';
    const FORM_HASH_KEY       = 'formHash';
    const HASH_PATTERN        = '/^(?P<formId>[a-zA-Z0-9]+)_(?P<pageIndex>[a-zA-Z0-9]+)_(?P<payload>.*)$/';

    const FORM_SESSION_TTL    = 10800; // 3 hours
    const ACTIVE_SESSIONS_KEY = 'freeformActiveSessions';

    const DEFAULT_PAGE_INDEX = 0;

    const DATA_DYNAMIC_RECIPIENTS_KEY = 'dynamicRecipients';
    const DATA_DYNAMIC_TEMPLATE_KEY   = 'dynamicTemplate';
    const DATA_SUBMISSION_TOKEN       = 'submissionToken';

    /** @var int */
    private $formId;

    /** @var int */
    private $currentPageIndex;

    /** @var array */
    private $storedValues;

    /** @var array */
    private $customFormData;

    /** @var SessionInterface */
    private $session;

    /** @var RequestInterface */
    private $request;

    /** @var string */
    private $lastHash;

    /**
     * @param string $hash
     *
     * @return int|null
     */
    public static function getFormIdFromHash($hash)
    {
        list($formIdHash) = self::getHashParts($hash);

        return $formIdHash ? HashHelper::decode($formIdHash) : null;
    }

    /**
     * @param string $hash
     *
     * @return int|null
     */
    public static function getPageIndexFromHash($hash)
    {
        list($_, $pageIndexHash) = self::getHashParts($hash);

        return HashHelper::decode($pageIndexHash);
    }

    /**
     * Returns an array of [formIdHash, pageIndexHash, payload]
     *
     * @param string $hash
     *
     * @return array
     */
    private static function getHashParts($hash)
    {
        if (preg_match(self::HASH_PATTERN, $hash, $matches)) {
            return [$matches['formId'], $matches['pageIndex'], $matches['payload']];
        }

        return [null, null, null];
    }

    /**
     * SessionFormContext constructor.
     *
     * @param int              $formId
     * @param SessionInterface $session
     * @param RequestInterface $request
     */
    public function __construct(
        $formId,
        SessionInterface $session,
        RequestInterface $request
    ) {
        $this->session          = $session;
        $this->request          = $request;
        $this->formId           = (int) $formId;
        $this->currentPageIndex = 0;
        $this->storedValues     = [];

        $this->lastHash = $this->regenerateHash();
        $this->regenerateState();
        $this->cleanUpOldSessions();
    }

    /**
     * @return string
     */
    public function getHash()
    {
        $this->lastHash = $this->regenerateHash();

        return $this->lastHash;
    }

    /**
     * @return string
     */
    public function getLastHash()
    {
        return $this->lastHash;
    }

    /**
     * @return int
     */
    public function getCurrentPageIndex()
    {
        return $this->currentPageIndex;
    }

    /**
     * @param int $currentPageIndex
     *
     * @return $this
     */
    public function setCurrentPageIndex($currentPageIndex)
    {
        $this->currentPageIndex = $currentPageIndex;
        $this->regenerateHash();

        return $this;
    }

    /**
     * @param AbstractField $field
     *
     * @return mixed|null
     */
    public function getStoredValue(AbstractField $field)
    {
        $fieldName = $field->getHandle();
        if (null === $fieldName) {
            return null;
        }

        if ($this->hasFormBeenPosted()) {
            if ($this->hasPageBeenPosted() && $field->getPageIndex() === $this->getCurrentPageIndex()) {
                return $this->request->getPost($fieldName);
            }

            if (isset($this->storedValues[$fieldName])) {
                return $this->storedValues[$fieldName];
            }
        }

        if ($field instanceof CheckboxField) {
            return $field->isChecked() ? $field->getValue() : '';
        }

        $default = $field->getValue();
        if (\is_string($default)) {
            $default = htmlspecialchars($default);
        }

        return $default;
    }

    /**
     * Checks whether the "PREVIOUS PAGE" button has been pressed
     *
     * @return bool
     */
    public function shouldFormWalkToPreviousPage()
    {
        if ($this->hasPageBeenPosted()) {
            return isset($_POST[SubmitField::PREVIOUS_PAGE_INPUT_NAME]);
        }

        return false;
    }

    /**
     * @param array|null $data
     *
     * @return $this
     */
    public function setCustomFormData(array $data = null)
    {
        $this->customFormData = $data;

        return $this;
    }

    /**
     * @return DynamicNotificationAttributes|null
     */
    public function getDynamicNotificationData()
    {
        if (
            isset(
                $this->customFormData[self::DATA_DYNAMIC_TEMPLATE_KEY],
                $this->customFormData[self::DATA_DYNAMIC_RECIPIENTS_KEY]
            )
        ) {
            $template   = $this->customFormData[self::DATA_DYNAMIC_TEMPLATE_KEY];
            $recipients = $this->customFormData[self::DATA_DYNAMIC_RECIPIENTS_KEY];

            return new DynamicNotificationAttributes(
                [
                    'template'   => $template,
                    'recipients' => $recipients,
                ]
            );
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getSubmissionIdentificator()
    {
        if (isset($this->customFormData[self::DATA_SUBMISSION_TOKEN])) {
            return $this->customFormData[self::DATA_SUBMISSION_TOKEN];
        }

        return null;
    }

    /**
     * @param array $storedValues
     *
     * @return FormValueContext
     */
    public function appendStoredValues($storedValues)
    {
        $this->storedValues = array_merge($this->storedValues, $storedValues);

        return $this;
    }

    /**
     * Advances the page index by 1
     */
    public function advanceToNextPage()
    {
        $this->currentPageIndex++;
        $this->regenerateHash();
    }

    /**
     * Walks back a single page
     */
    public function retreatToPreviousPage()
    {
        $this->currentPageIndex--;
        $this->regenerateHash();
    }

    /**
     * Save current state in session
     */
    public function saveState()
    {
        $encodedData    = json_encode($this, JSON_OBJECT_AS_ARRAY);
        $sessionHashKey = $this->getSessionHash($this->getHash());

        $this->session->set($sessionHashKey, $encodedData);
        $this->appendKeyToActiveSessions($sessionHashKey);
    }

    /**
     * Removes the current key from active session list
     */
    public function cleanOutCurrentSession()
    {
        $sessionHashKey = $this->getSessionHash($this->getHash());
        $this->session->remove($sessionHashKey);
    }

    /**
     * Attempts to regenerate existing state
     */
    public function regenerateState()
    {
        $sessionHash  = $this->getSessionHash();
        $sessionState = $this->session->get($sessionHash);

        if ($sessionHash && $sessionState) {
            $sessionState = json_decode($sessionState, true);

            $this->currentPageIndex = $sessionState['currentPageIndex'];
            $this->storedValues     = $sessionState['storedValues'];
            $this->customFormData   = $sessionState['customFormData'];
        } else {
            $this->currentPageIndex = self::DEFAULT_PAGE_INDEX;
            $this->storedValues     = [];
            $this->customFormData   = [];
        }
    }

    /**
     * Check if the current form has been posted
     *
     * @return bool
     */
    public function hasFormBeenPosted()
    {
        $postedHash = $this->getPostedHash();

        if (null === $postedHash) {
            return false;
        }

        list($_, $_, $postedPayload) = self::getHashParts($postedHash);
        list($_, $_, $currentPayload) = self::getHashParts($this->getHash());

        return $postedPayload === $currentPayload;
    }

    /**
     * Check if the current form has been posted
     *
     * @return bool
     */
    public function hasPageBeenPosted()
    {
        $postedHash = $this->getPostedHash();

        if (null === $postedHash || !$this->hasFormBeenPosted()) {
            return false;
        }

        list($_, $postedPageIndex, $postedPayload) = self::getHashParts($postedHash);
        list($_, $currentPageIndex, $currentPayload) = self::getHashParts($this->getHash());

        return $postedPageIndex === $currentPageIndex && $postedPayload === $currentPayload;
    }

    /**
     * @return string|null
     */
    private function getPostedHash()
    {
        return $this->request->getPost(self::FORM_HASH_KEY);
    }

    /**
     * @param string $hash - provide an existing hash, otherwise takes it from POST
     *
     * @return string|null
     */
    private function getSessionHash($hash = null)
    {
        if (null === $hash) {
            $hash = $this->getPostedHash();
        }

        list($formIdHash, $_, $payload) = self::getHashParts($hash);

        if ($formIdHash === $this->hashFormId()) {
            return sprintf(
                '%s%s%s',
                $formIdHash,
                self::FORM_HASH_DELIMITER,
                $payload
            );
        }

        return null;
    }

    /**
     * Generates a random hash for identification
     *
     * @return string
     */
    private function regenerateHash()
    {
        // Attempt to fetch hashes from POST data
        list($formIdHash, $_, $payload) = self::getHashParts($this->getPostedHash());

        $formId           = self::getFormIdFromHash($this->getPostedHash());
        $isFormIdMatching = (int) $formId === (int) $this->formId;

        // Only generate a new hash if the content indexes don' match with the posted hash
        // Or if there is no posted hash
        $generateNew = !$isFormIdMatching || !($formIdHash && $payload);

        if ($generateNew) {
            $random  = time() . mt_rand(111, 999);
            $hash    = sha1($random);
            $payload = uniqid($hash, false);

            $formIdHash = HashHelper::hash($this->formId);
        }

        $hashedPageIndex = $this->hashPageIndex();

        $hash = sprintf(
            '%s%s%s%s%s',
            $formIdHash,
            self::FORM_HASH_DELIMITER,
            $hashedPageIndex,
            self::FORM_HASH_DELIMITER,
            $payload
        );
        $hash = htmlentities($hash, ENT_QUOTES, 'UTF-8');

        return $hash;
    }

    /**
     * @return string
     */
    private function hashFormId()
    {
        return HashHelper::hash($this->formId);
    }

    /**
     * @return string
     */
    private function hashPageIndex()
    {
        return HashHelper::sha1($this->currentPageIndex, 4, 10);
    }

    /**
     * Cleans up all old session instances
     */
    private function cleanUpOldSessions()
    {
        $instances = $this->getActiveSessionList();

        foreach ($instances as $time => $hash) {
            if ($time < time() - self::FORM_SESSION_TTL) {
                $this->session->remove($hash);
                unset($instances[$time]);
            }
        }

        $this->session->set(self::ACTIVE_SESSIONS_KEY, json_encode($instances));
    }

    /**
     * Gets the active session list
     * [time => sessionHash, ..]
     *
     * @return array
     */
    private function getActiveSessionList()
    {
        $activeSessionList = json_decode(
            $this->session->get(self::ACTIVE_SESSIONS_KEY, '[]'),
            true
        );

        if (!is_array($activeSessionList)) {
            return [];
        }

        return $activeSessionList;
    }

    /**
     * Appends a session hash to active instances, for cleanup later
     *
     * @param string $sessionHash
     */
    private function appendKeyToActiveSessions($sessionHash)
    {
        $instances = $this->getActiveSessionList();

        $instances[time()] = $sessionHash;

        $this->session->set(self::ACTIVE_SESSIONS_KEY, json_encode($instances));
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'currentPageIndex' => $this->currentPageIndex,
            'storedValues'     => $this->storedValues,
            'customFormData'   => $this->customFormData,
        ];
    }
}
