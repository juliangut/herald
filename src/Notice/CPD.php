<?php

namespace Herald\Notice;

class CPD extends Notice
{
    /**
     * {@inheritDoc}
     */
    protected $report = 'PMD-CPD';

    /**
     * {@inheritDoc}
     */
    protected $severity = Notice::SEVERITY_WARNING;
}
