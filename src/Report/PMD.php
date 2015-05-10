<?php

namespace Herald\Report;

class PMD extends Report
{
    /**
     * {@inheritDoc}
     */
    protected $name = 'PMD';

    /**
     * {@inheritDoc}
     */
    protected $reportParsers = [
        'pmd.xml'   => 'Herald\\Parser\\PMD\\XML',
        'phpmd.xml' => 'Herald\\Parser\\PMD\\XML',
    ];
}
