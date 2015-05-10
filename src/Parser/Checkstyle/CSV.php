<?php

namespace Herald\Parser\Checkstyle;

use Herald\Parser\CSV as CSVParser;
use Herald\Notice\Notice;
use Herald\Notice\Checkstyle as CheckstyleNotice;

class CSV extends CSVParser
{
    /**
     * {@inheritDoc}
     */
    public function loadFile($file)
    {
        parent::loadFile($file);

        $headLine = ['File', 'Line', 'Column', 'Type', 'Message', 'Source', 'Severity', 'Fixable'];

        if (!count($this->document) || $this->document[0] !== $headLine) {
            throw new \RuntimeException(sprintf('File "%s" is not a valid CSV file', $file));
        }

        array_shift($this->document);
    }

    /**
     * {@inheritDoc}
     */
    public function getNotices()
    {
        if (!count($this->document)) {
            return [];
        }

        $notices = [];

        foreach ($this->document as $file) {
            $notice = new CheckstyleNotice;

            $notice->setSeverity($file[3] === 'error' ? Notice::SEVERITY_ERROR : Notice::SEVERITY_WARNING);
            $notice->setFile($file[0]);
            $notice->setLineStart($file[1]);
            $notice->setLineEnd($file[1]);
            $notice->setDescription($file[4]);

            $notices[] = $notice;
        }

        return $notices;
    }
}
