<?php

namespace Solspace\Addons\FreeformNext\Controllers;

use Solspace\Addons\FreeformNext\Library\Exceptions\FreeformException;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\CpView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\RedirectView;
use Solspace\Addons\FreeformNext\Utilities\ControlPanel\View;

class LogController extends Controller
{
    /**
     * @param string      $logName
     * @param string|null $action
     *
     * @return View
     * @throws FreeformException
     */
    public function view($logName, $action = null)
    {
        $dir      = __DIR__ . '/../logs/';
        $filePath = $dir . $logName . '.log';

        if (!file_exists($filePath)) {
            throw new FreeformException('Logfile not found');
        }

        if ($action === 'delete') {
            @unlink($filePath);

            return new RedirectView($this->getLink('/'));
        }

        $view = new CpView(
            'logs/log',
            [
                'content'          => $this->getParsedLogContent($filePath),
                'cp_page_title'    => $logName . ' log',
                'form_right_links' => [
                    [
                        'title' => sprintf('Clear "%s" logfile', $logName),
                        'link'  => $this->getLink('logs/' . $logName . '/delete'),
                    ],
                ],
            ]
        );

        $view
            ->setHeading($logName)
            ->addBreadcrumb(new NavigationLink('Logs', 'logs/' . $logName))
            ->addCss('logs');

        return $view;
    }

    /**
     * @param string $filePath
     *
     * @return string
     */
    private function getParsedLogContent($filePath)
    {
        $content = [];

        if ($v = @fopen($filePath, 'rb')) { //open the file
            $messageBuffer = '';

            fseek($v, 0, SEEK_END); //move cursor to the end of the file

            while (ftell($v) > 0) {
                $newLine     = false;
                $charCounter = 0;

                while (!$newLine && $this->moveOneStepBack($v)) { //not start of a line / the file
                    if ($this->readNotSeek($v, 1) === "\n") {
                        $newLine = true;
                    }
                    $charCounter++;
                }

                if ($charCounter > 1) { //if there was anything on the line
                    $line = $this->readNotSeek($v, $charCounter); //prints current line

                    if (preg_match('/^\s*([0-9-T:+]+)\s([\w]+)\s+([\w\d_]+)\s+(.*)$/', $line, $matches)) {
                        list($_, $date, $level, $category, $message) = $matches;

                        $content[] = [
                            'date'     => new \DateTime($date),
                            'level'    => $level,
                            'category' => $category,
                            'message'  => $messageBuffer . $message,
                        ];

                        $messageBuffer = '';
                    } else {
                        $messageBuffer .= "\n" . ltrim($line);
                    }
                }

            }
            fclose($v); //close the file, because we are well-behaved
        }

        return $content;
    }

    /**
     * Moves cursor one step back if can - returns true, if can't - returns false
     *
     * @param resource $handle
     *
     * @return bool
     */
    private function moveOneStepBack(&$handle)
    {
        if (ftell($handle) > 0) {
            fseek($handle, -1, SEEK_CUR);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Reads $length chars but moves cursor back where it was before reading
     *
     * @param resource $handle
     * @param int      $length
     *
     * @return bool|string
     */
    private function readNotSeek(&$handle, $length)
    {
        $r = fread($handle, $length);
        fseek($handle, -$length, SEEK_CUR);

        return $r;
    }
}
