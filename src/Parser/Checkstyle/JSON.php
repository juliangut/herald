<?php

namespace Herald\Parser\Checkstyle;

use Herald\Parser\JSON as JSONParser;
use Herald\Notice\Notice;
use Herald\Notice\Checkstyle as CheckstyleNotice;

class JSON extends JSONParser
{
    /**
     * {@inheritDoc}
     */
    public function loadFile($file)
    {
        parent::loadFile($file);
    }

    /**
     * {@inheritDoc}
     */
    public function getNotices()
    {
        if (!isset($this->document->files)) {
            return [];
        }

        $notices = [];

        foreach ($this->document->files as $filePath => $file) {
            foreach ($file->messages as $item) {
                $notice = new CheckstyleNotice;

                $notice->setSeverity($item->type === 'ERROR' ? Notice::SEVERITY_ERROR : Notice::SEVERITY_WARNING);
                $notice->setFile($filePath);
                $notice->setLineStart($item->line);
                $notice->setLineEnd($item->line);
                $notice->setDescription($item->message);

                $notices[] = $notice;
            }
        }

        return $notices;
    }
}
