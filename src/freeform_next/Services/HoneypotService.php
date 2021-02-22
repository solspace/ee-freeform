<?php

namespace Solspace\Addons\FreeformNext\Services;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Library\DataObjects\FormRenderObject;
use Solspace\Addons\FreeformNext\Library\Session\EESession;
use Solspace\Addons\FreeformNext\Library\Session\Honeypot;

class HoneypotService
{
    const FORM_HONEYPOT_KEY  = 'freeformHoneypotHashList';
    const FORM_HONEYPOT_NAME = 'form_name_handle';

    const MAX_HONEYPOT_TTL   = 10800; // 3 Hours
    const MAX_HONEYPOT_COUNT = 100;   // Limit the number of maximum honeypot values per session

    /** @var array */
    private static $validHoneypots = [];

    /** @var Honeypot[] */
    private $honeypotCache = [];

    /**
     * Adds honeypot javascript to forms
     *
     * @param FormRenderObject $renderObject
     */
    public function addFormJavascript(FormRenderObject $renderObject)
    {
        $isHoneypotEnabled = $this->getSettingsService()->getSettingsModel()->isSpamProtectionEnabled();

        if ($isHoneypotEnabled) {
            $script = $this->getHoneypotJavascriptScript($renderObject->getForm());
            $renderObject->appendJsToOutput($script);
        }
    }

    /**
     * Assembles a honeypot field
     *
     * @param FormRenderObject $renderObject
     */
    public function addHoneyPotInputToForm(FormRenderObject $renderObject)
    {
        $renderObject->appendToOutput($this->getHoneypotInput($renderObject->getForm()));
    }

    /**
     * @param Form $form
     */
    public function validateFormHoneypot(Form $form)
    {
        if (!$this->getSettingsService()->getSettingsModel()->isSpamProtectionEnabled()) {
            return;
        }

        /** @var array $postValues */
        $postValues = $_POST;

        if(!$this->getSettingsService()->getSettingsModel()->isFreeformHoneypotEnhanced())
		{
			if (array_key_exists(Honeypot::NAME_PREFIX, $postValues) && $postValues[Honeypot::NAME_PREFIX] === '') {
				return;
			}
		}
		else
		{
			foreach ($postValues as $key => $value) {
				if (strpos($key, Honeypot::NAME_PREFIX) === 0) {
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

        $form->setMarkedAsSpam(true);
    }

    /**
     * @param Form $form
     *
     * @return string
     */
    public function getHoneypotJavascriptScript(Form $form)
    {
        $honeypot = $this->getHoneypot($form);

        return 'var o = document.getElementsByName("' . $honeypot->getName() . '"); for (var i in o) { if (!o.hasOwnProperty(i)) {continue;} o[i].value = "' . $honeypot->getHash() . '"; }';
    }

    /**
     * @param Form $form
     *
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
    private function getNewHoneypot()
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
     * @param array $honeypotList
     *
     * @return array
     */
    private function weedOutOldHoneypots(array $honeypotList)
    {
		if (!$this->isEnhanced()) {
			return [];
		}

        $cleanList = array_filter(
            $honeypotList,
            function (Honeypot $honeypot) {
                return $honeypot->getTimestamp() > (time() - self::MAX_HONEYPOT_TTL);
            }
        );

        usort(
            $cleanList,
            function (Honeypot $a, Honeypot $b) {
                if ($a->getTimestamp() === $b->getTimestamp()) {
                    return 0;
                }

                return ($a->getTimestamp() < $b->getTimestamp()) ? 1 : -1;
            }
        );

        if (\count($cleanList) > self::MAX_HONEYPOT_COUNT) {
            $cleanList = \array_slice($cleanList, 0, self::MAX_HONEYPOT_COUNT);
        }

        return $cleanList;
    }

    /**
     * Removes a honeypot from the list once it has been validated
     *
     * @param Honeypot $honeypot
     */
    private function removeHoneypot(Honeypot $honeypot)
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

    /**
     * @param array $honeypotList
     */
    private function updateHoneypotList(array $honeypotList)
    {
        $this->getSession()->set(self::FORM_HONEYPOT_KEY, json_encode($honeypotList));
    }

    /**
     * @return SettingsService
     */
    private function getSettingsService()
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
     * @param Form $form
     *
     * @return string
     */
    public function getHoneypotInput(Form $form)
    {
        static $honeypotHashes = [];

        if (!isset($honeypotHashes[$form->getHash()])) {
            $random                           = time() . mt_rand(0, 999) . (time() + 999);
            $honeypotHashes[$form->getHash()] = substr(sha1($random), 0, 6);
        }

        $hash = $honeypotHashes[$form->getHash()];

        $honeypot     = $this->getHoneypot($form);
        $honeypotName = $honeypot->getName();
        $output       = '<input '
            . 'type="text" '
            . 'value="' . ($this->isEnhanced() ? $hash : '') . '" '
            . 'name="' . $honeypotName . '" '
            . 'id="' . $honeypotName . '" '
            . '/>';

        $output = '<div style="position: absolute !important; width: 0 !important; height: 0 !important; overflow: hidden !important;" aria-hidden="true" tabindex="-1">'
            . '<label for="' . $honeypotName . '">Leave this field blank</label>'
            . $output
            . '</div>';
        return $output;
    }

	/**
	 * @return bool
	 */
	private function isEnhanced(): bool
	{
		return $this->getSettingsService()->getSettingsModel()->isFreeformHoneypotEnhanced();
	}
}
