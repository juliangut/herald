<?php

namespace Herald\Report;

class Coverage extends Report
{
    /**
     * {@inheritDoc}
     */
    protected $name = 'Coverage';

    /**
     * {@inheritDoc}
     */
    protected $reportParsers = [
        'clover.xml'   => 'Herald\\Parser\\Coverage\\XML',
        'coverage.xml' => 'Herald\\Parser\\Coverage\\XML',
    ];
}
