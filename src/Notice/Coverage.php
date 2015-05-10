<?php

namespace Herald\Notice;

class Coverage extends Notice
{
    /**
     * {@inheritDoc}
     */
    protected $report = 'Coverage';

    /**
     * {@inheritDoc}
     */
    protected $severity = Notice::SEVERITY_INFO;

    /**
     * Number of tests coverage
     *
     * @var int
     */
    protected $count;

    /**
     * Set number of tests coverage
     *
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = (int) $count;
    }

    /**
     * Get number of tests coverage
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
}
