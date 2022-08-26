<?php
/**
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2022, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\FreeformNext\Library\Session;

class DbSession implements SessionInterface
{
    /**
     * @param string     $key
     * @param mixed|null $defaultValue
     *
     * @return mixed
     */
    public function get($key, $defaultValue = null)
    {
        if (null === $key) {
            return $defaultValue;
        }

        $query = $this->getQueryInstance($key);

        if ($query->num_rows() > 0) {
            $value = $query->row()->data;

            if (null === $value) {
                return $defaultValue;
            }

            return $value;
        }

        return $defaultValue;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $valueExists = $this->getQueryInstance($key)->num_rows() > 0;
        $sessionId   = $this->getSessionId();
        $date        = (new \DateTime())->format('Y-m-d H:i:s');

        if ($valueExists) {
            ee()->db
                ->where(
                    [
                        'key'       => $key,
                        'sessionId' => $sessionId,
                    ]
                )
                ->update(
                    'exp_freeform_next_session_data',
                    [
                        'data'        => $value,
                        'dateCreated' => $date,
                    ]
                );
        } else {
            ee()->db
                ->insert(
                    'exp_freeform_next_session_data',
                    [
                        'key'         => $key,
                        'sessionId'   => $sessionId,
                        'data'        => $value,
                        'dateCreated' => $date,
                    ]
                );
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function remove($key)
    {
        ee()->db
            ->delete(
                'exp_freeform_next_session_data',
                [
                    'key'       => $key,
                    'sessionId' => $this->getSessionId(),
                ]
            );

        return true;
    }

    /**
     * @param string $key
     *
     * @return \CI_DB_mysqli_result
     */
    private function getQueryInstance($key)
    {
        return ee()->db
            ->select('data')
            ->from('exp_freeform_next_session_data')
            ->where(
                [
                    'key'       => $key,
                    'sessionId' => $this->getSessionId(),
                ]
            )
            ->get();
    }

    /**
     * @return string
     */
    private function getSessionId()
    {
        return session_id();
    }
}
