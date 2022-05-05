<?php

namespace Solspace\Addons\FreeformNext\Library\DataObjects;

use Solspace\Addons\FreeformNext\Library\Composer\Components\Form;
use Solspace\Addons\FreeformNext\Model\StatusModel;
use Solspace\Addons\FreeformNext\Model\SubmissionModel;

class SubmissionAttributes
{
    /** @var int */
    private $siteId;

    /** @var Form */
    private $form;

    /** @var int */
    private $submissionId;

    /** @var string */
    private $token;

    /** @var int */
    private $limit;

    /** @var int */
    private $offset;

    /** @var string */
    private $orderBy;

    /** @var string */
    private $sort;

    /** @var string */
    private $status;

    /** @var \DateTime */
    private $dateRangeStart;

    /** @var \DateTime */
    private $dateRangeEnd;

    /** @var array */
    private $filters;

    /** @var array */
    private $orFilters;

    /** @var array */
    private $likeFilters;

    /** @var array */
    private $orLikeFilters;

    /** @var array */
    private $idFilters;

    /** @var array */
    private $orIdFilters;

    /** @var array */
    private $inFilters;

    /** @var array */
    private $notInFilters;

    /** @var array */
    private $where;

    /**
     * SubmissionAttributes constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;

        $this->filters = [
            SubmissionModel::TABLE . '.formId' => $form->getId(),
        ];

        $this->orFilters     = [];
        $this->likeFilters   = [];
        $this->orLikeFilters = [];
        $this->idFilters     = [];
        $this->orIdFilters   = [];
        $this->inFilters     = [];
        $this->notInFilters  = [];
        $this->where         = [];
    }

    /**
     * @return int
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param int $siteId
     *
     * @return $this
     */
    public function setSiteId($siteId = null)
    {
        $this->siteId = $siteId;

        return $this;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return int
     */
    public function getSubmissionId()
    {
        return $this->submissionId;
    }

    /**
     * @param int $submissionId
     *
     * @return $this
     */
    public function setSubmissionId($submissionId = null)
    {
        $this->submissionId = $submissionId;
        $this->setFilter(SubmissionModel::TABLE . '.id', $submissionId);

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return SubmissionAttributes
     */
    public function setToken($token = null)
    {
        $this->token = $token;
        $this->setFilter(SubmissionModel::TABLE . '.token', $token);

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit($limit = null)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     *
     * @return $this
     */
    public function setOffset($offset = null)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderBy()
    {
        $orderBy = $this->orderBy;

        if (null === $orderBy || false === $orderBy) {
            return 'dateCreated';
        }

        if ($orderBy === 'status') {
            return 'statusName';
        }

        if ($orderBy === 'date') {
            return 'dateCreated';
        }

        if ($orderBy && !in_array($orderBy, ['id', 'title', 'status'], true)) {
            foreach ($this->form->getLayout()->getFields() as $field) {
                if ($orderBy === $field->getHandle()) {
                    return SubmissionModel::getFieldColumnName($field->getId());
                }
            }
        }

        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     *
     * @return $this
     */
    public function setOrderBy($orderBy = null)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param string $sort
     *
     * @return $this
     */
    public function setSort($sort = null)
    {
        $this->sort = strtolower((string) $sort) === 'desc' ? 'DESC' : 'ASC';

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status = null)
    {
        $this->status = $status;
        $this->setFilter(StatusModel::TABLE . '.name', $status);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateRangeStart()
    {
        return $this->dateRangeStart;
    }

    /**
     * @param \DateTime $dateRangeStart
     *
     * @return $this
     */
    public function setDateRangeStart($dateRangeStart)
    {
        $dateRangeStart = $this->getDateValue($dateRangeStart);

        if ($dateRangeStart) {
            $this->dateRangeStart = $dateRangeStart;
            $this->setFilter(SubmissionModel::TABLE . '.dateCreated >=', $dateRangeStart);
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateRangeEnd()
    {
        return $this->dateRangeEnd;
    }

    /**
     * @param \DateTime $dateRangeEnd
     *
     * @return $this
     */
    public function setDateRangeEnd($dateRangeEnd)
    {
        if ($dateRangeEnd) {
            $dateRangeEnd = str_replace('00:00:00', '23:59:59', $this->getDateValue($dateRangeEnd));

            $this->dateRangeEnd = $dateRangeEnd;
            $this->setFilter(SubmissionModel::TABLE . '.dateCreated <=', $dateRangeEnd);
        }

        return $this;
    }

    /**
     * @param string $string
     *
     * @return $this
     */
    public function setDateRange($string)
    {
        if (null === $string) {
            return $this;
        }

        switch (strtolower($string)) {
            case 'today':
                $start = new \DateTime();
                $start->setTime(0, 0, 0);

                $end = clone $start;
                $end->setTime(23, 59, 59);

                $this
                    ->setDateRangeStart($start)
                    ->setDateRangeEnd($end);

                break;

            case 'this week':
                $day   = date('w');
                $start = date('Y-m-d 00:00:00', strtotime('-' . $day . ' days'));
                $end   = date('Y-m-d 23:59:59', strtotime('+' . (6 - $day) . ' days'));

                $this
                    ->setDateRangeStart($start)
                    ->setDateRangeEnd($end);

                break;

            case 'this month':
                $maxDays = date('t');
                $start   = date('Y-m-01 00:00:00');
                $end     = date('Y-m-' . $maxDays . ' 23:59:59');

                $this
                    ->setDateRangeStart($start)
                    ->setDateRangeEnd($end);

                break;

            case 'last month':
                $timeLastMonth = strtotime('last month');

                $maxDays = date('t', $timeLastMonth);
                $start   = date('Y-m-01 00:00:00', $timeLastMonth);
                $end     = date('Y-m-' . $maxDays . ' 23:59:59', $timeLastMonth);

                $this
                    ->setDateRangeStart($start)
                    ->setDateRangeEnd($end);

                break;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getLikeFilters()
    {
        return $this->likeFilters;
    }

    /**
     * @return array
     */
    public function getOrLikeFilters()
    {
        return $this->orLikeFilters;
    }

    /**
     * @return array
     */
    public function getIdFilters()
    {
        return $this->idFilters;
    }

    /**
     * @return array
     */
    public function getOrIdFilters()
    {
        return $this->orIdFilters;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return array
     */
    public function getOrFilters()
    {
        return $this->orFilters;
    }

    /**
     * @return array
     */
    public function getInFilters()
    {
        return $this->inFilters;
    }

    /**
     * @return array
     */
    public function getNotInFilters()
    {
        return $this->notInFilters;
    }

    /**
     * @return array
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addFilter($key, $value)
    {
        if (false !== $this->getNotInArray($value)) {
            $this->notInFilters[$key] = $value;
        } else if (false !== $this->getInArray($value)) {
            $this->inFilters[$key] = $value;
        } else {
            $this->filters[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addOrFilter($key, $value)
    {
        $this->orFilters[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addLikeFilter($key, $value)
    {
        $this->likeFilters[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addOrLikeFilter($key, $value)
    {
        $this->orLikeFilters[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addIdFilter($key, $value)
    {
        $this->idFilters[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addOrIdFilter($key, $value)
    {
        $this->orIdFilters[$key] = $value;

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function addWhere($value)
    {
        $this->where[] = $value;

        return $this;
    }

    /**
     * @param string $string
     *
     * @return array|bool
     */
    private function getInArray($string)
    {
        if (false !== $this->getNotInArray($string)) {
            return false;
        }

        if (strpos($string, '|') === false) {
            return false;
        }

        return explode('|', $string);
    }

    /**
     * @param string $string
     *
     * @return array|bool
     */
    private function getNotInArray($string)
    {
        if (strpos($string, 'not ') !== 0) {
            return false;
        }

        $string = substr($string, 4);

        return explode('|', $string);
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    private function setFilter($key, $value)
    {
        unset($this->filters[$key], $this->inFilters[$key], $this->notInFilters[$key]);

        if (null === $value) {
            return;
        }

        if (false !== $this->getNotInArray($value)) {
            $this->notInFilters[$key] = $this->getNotInArray($value);
        } else if (false !== $this->getInArray($value)) {
            $this->inFilters[$key] = $this->getInArray($value);
        } else {
            $this->filters[$key] = $value;
        }
    }

    /**
     * Takes a string or DateTime intsance and returns a
     * 'Y-m-d H:i:s' string of that date
     *
     * @param \DateTime|string $date
     *
     * @return string|null
     */
    private function getDateValue($date)
    {
        if (null === $date) {
            return null;
        }

        if ($date instanceof \DateTime) {
            return $date->format('Y-m-d H:i:s');
        }

        return date('Y-m-d H:i:s', strtotime($date));
    }
}
