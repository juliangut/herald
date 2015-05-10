<?php

namespace Herald\Notice;

class Checkstyle extends Notice
{
    /**
     * {@inheritDoc}
     */
    protected $report = 'Checkstyle';

    /**
     * {@inheritDoc}
     */
    protected $severity = Notice::SEVERITY_WARNING;
}
