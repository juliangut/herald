<?php

namespace Herald\Report;

class CRAP extends Report
{
    /**
     * {@inheritDoc}
     */
    protected $name = 'CRAP';

    /**
     * {@inheritDoc}
     */
    protected $reportParsers = [
        'clover.xml'   => 'Herald\\Parser\\CRAP\\XML',
        'coverage.xml' => 'Herald\\Parser\\CRAP\\XML',
    ];
}
