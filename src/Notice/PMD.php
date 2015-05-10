<?php

namespace Herald\Notice;

class PMD extends Notice
{
    /**
     * {@inheritDoc}
     */
    protected $report = 'PMD';

    /**
     * {@inheritDoc}
     */
    protected $severity = Notice::SEVERITY_ERROR;
}
