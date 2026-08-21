<?php

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\DataObjects\FormRenderObject;
use Solspace\Addons\FreeformNext\Library\Session\EESession;
use Solspace\Addons\FreeformNext\Library\Session\Honeypot;
use Solspace\Addons\FreeformNext\Model\SpamReasonModel;

class HoneypotService
{
    public const FORM_HONEYPOT_KEY  = 'freeformHoneypotHashList';
    public const FORM_HONEYPOT_NAME = 'form_name_handle';

    public const MAX_HONEYPOT_TTL   = 10800; // 3 Hours
    public const MAX_HONEYPOT_COUNT = 100;   // Limit the number of maximum honeypot values per session
    private static array $validHoneypots = [];

    /** @var Honeypot[] */
    private array $honeypotCache = [];

    /**
     * Adds honeypot javascript to forms
     */
    public function addFormJavascript(FormRenderObject $renderObject): void
    {
        $isHoneypotEnabled = $this->getSettingsService()->getSettingsModel()->isSpamProtectionEnabled();

        if ($isHoneypotEnabled) {
            $script = $this->getHoneypotJavascriptScript($renderObject->getForm());
            $renderObject->appendJsToOutput($script);
        }
    }

    /**
     * Assembles a honeypot field
     */
    public function addHoneyPotInputToForm(FormRenderObject $renderObject): void
    {
        $renderObject->appendToOutput($this->getHoneypotInput($renderObject->getForm()));
    }

    public function validateFormHoneypot(Form $form): void
    {
        if (!$this->getSettingsService()->getSettingsModel()->isSpamProtectionEnabled()) {
            return;
        }

        /** @var array $postValues */
        $postValues = $_POST;

        if (!$this->getSettingsService()->getSettingsModel()->isFreeformHoneypotEnhanced()) {
            if (array_key_exists(Honeypot::NAME_PREFIX, $postValues) && $postValues[Honeypot::NAME_PREFIX] === '') {
                return;
            }
		} else {
            foreach ($postValues as $key => $value) {
                if (str_starts_with($key, Honeypot::NAME_PREFIX)) {
                    if (\in_array($key, self::$validHoneypots, true)) {
                        return;
                    }

                    $honeypotList = $this->getHoneypotList();
                    foreach ($honeypotList as $honeypot) {
                        $hasMatchingName = $key === $honeypot->getName();
                        $hasMatchingHash = $value === $honeypot->getHash();
                        if ($hasMatchingName && $hasMatchingHash) {
                            self::$validHoneypots[] = $key;

                            $this->removeHoneypot($honeypot);

                            return;
                        }
                    }
                }
            }
        }

        if (!$this->getSettingsService()->getSettingsModel()->spamBlockLikeSuccessfulPost) {
            $form->addError(lang('Form honeypot is invalid'));
        }

        $form->setMarkedAsSpam(
            SpamReasonModel::TYPE_HONEYPOT,
            'Honeypot check failed',
            $postValues[Honeypot::NAME_PREFIX] ?? ''
        );
    }

    /**
     * @return string
     */
    public function getHoneypotJavascriptScript(Form $form): string
    {
        $honeypot = $this->getHoneypot($form);

        return 'var o = document.getElementsByName("' . $honeypot->getName() . '"); for (var i in o) { if (!o.hasOwnProperty(i)) {continue;} o[i].value = "' . $honeypot->getHash() . '"; }';
    }

    /**
     * @return Honeypot
     */
    public function getHoneypot(Form $form)
    {
        $hash = $form->getHash();

        if (!isset($this->honeypotCache[$hash])) {
            $this->honeypotCache[$hash] = $this->getNewHoneypot();
        }

        return $this->honeypotCache[$hash];
    }

    /**
     * @return Honeypot
     */
    private function getNewHoneypot(): Honeypot
    {
        $honeypot = new Honeypot($this->isEnhanced());

        if ($this->isEnhanced()) {
            $honeypotList   = $this->getHoneypotList();
            $honeypotList[] = $honeypot;
            $honeypotList   = $this->weedOutOldHoneypots($honeypotList);
            $this->updateHoneypotList($honeypotList);
        }

        return $honeypot;
    }

    /**
     * @return Honeypot[]
     */
    private function getHoneypotList()
    {
        $sessionHoneypotList = json_decode($this->getSession()->get(self::FORM_HONEYPOT_KEY, '[]'), true);
        if (!empty($sessionHoneypotList)) {
            foreach ($sessionHoneypotList as $index => $unserialized) {
                $sessionHoneypotList[$index] = Honeypot::createFromUnserializedData($unserialized);
            }
        }

        return $sessionHoneypotList;
    }

    /**
     * @return array
     */
    private function weedOutOldHoneypots(array $honeypotList)
    {
        if (!$this->isEnhanced()) {
            return [];
        }

        $cleanList = array_filter(
            $honeypotList,
            fn(Honeypot $honeypot): bool => $honeypot->getTimestamp() > (time() - self::MAX_HONEYPOT_TTL)
        );

        usort(
            $cleanList,
            fn(Honeypot $a, Honeypot $b): int => $b->getTimestamp() <=> $a->getTimestamp()
        );

        if (\count($cleanList) > self::MAX_HONEYPOT_COUNT) {
            $cleanList = \array_slice($cleanList, 0, self::MAX_HONEYPOT_COUNT);
        }

        return $cleanList;
    }

    /**
     * Removes a honeypot from the list once it has been validated
     */
    private function removeHoneypot(Honeypot $honeypot): void
    {
        $list = $this->getHoneypotList();

        foreach ($list as $index => $listHoneypot) {
            if ($listHoneypot->getName() === $honeypot->getName()) {
                unset($list[$index]);

                break;
            }
        }

        $this->updateHoneypotList($list);
    }

    private function updateHoneypotList(array $honeypotList): void
    {
        $this->getSession()->set(self::FORM_HONEYPOT_KEY, json_encode($honeypotList));
    }

    /**
     * @return SettingsService
     */
    private function getSettingsService(): SettingsService
    {
        return new SettingsService();
    }

    /**
     * @return EESession
     */
    private function getSession()
    {
        static $session;

        if (null === $session) {
            $session = new EESession();
        }

        return $session;
    }

    /**
     * @return string
     */
    public function getHoneypotInput(Form $form): string
    {
        static $honeypotHashes = [];

        $formHash = $form->getHash();

        if (!isset($honeypotHashes[$formHash])) {
            $random = time() . random_int(0, 999) . (time() + 999);
            $honeypotHashes[$formHash] = substr(sha1($random), 0, 6);
        }

        $honeypotName = $this->getHoneypot($form)->getName();
        $value = $this->isEnhanced() ? $honeypotHashes[$formHash] : '';

        $input = '<input '
            . 'type="text" '
            . 'aria-hidden="true" '
            . 'tabindex="-1" '
            . 'autocomplete="off" '
            . 'value="' . $value . '" '
            . 'name="' . $honeypotName . '" '
            . 'id="' . $honeypotName . '" '
            . '/>';

        return '<div '
            . 'style="position: absolute !important; width: 0 !important; height: 0 !important; overflow: hidden !important;" '
            . 'aria-hidden="true" '
            . 'tabindex="-1">'
            . '<label for="' . $honeypotName . '">Leave this field blank</label>'
            . $input
            . '</div>';
    }

    /**
     * @return bool
     */
    private function isEnhanced(): bool
    {
        return $this->getSettingsService()->getSettingsModel()->isFreeformHoneypotEnhanced();
    }
}
