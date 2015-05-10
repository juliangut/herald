<?php

namespace Herald\Report;

class Checkstyle extends Report
{
    /**
     * {@inheritDoc}
     */
    protected $name = 'Checkstyle';

    /**
     * {@inheritDoc}
     */
    protected $reportParsers = [
        'checkstyle.xml'  => 'Herald\\Parser\\Checkstyle\\XML',
        'phpcs.xml'       => 'Herald\\Parser\\Checkstyle\\XML',
        'checkstyle.json' => 'Herald\\Parser\\Checkstyle\\JSON',
        'phpcs.json'      => 'Herald\\Parser\\Checkstyle\\JSON',
        'checkstyle.csv'  => 'Herald\\Parser\\Checkstyle\\CSV',
        'phpcs.csv'       => 'Herald\\Parser\\Checkstyle\\CSV',
    ];
}
