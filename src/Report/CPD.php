<?php

namespace Herald\Report;

class CPD extends Report
{
    /**
     * {@inheritDoc}
     */
    protected $name = 'PMD-CPD';

    /**
     * {@inheritDoc}
     */
    protected $reportParsers = [
        'pmd-cpd.xml' => 'Herald\\Parser\\CPD\\XML',
        'phpcpd.xml'  => 'Herald\\Parser\\CPD\\XML',
    ];
}
