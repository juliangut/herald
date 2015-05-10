<?php

namespace Herald\Parser\PMD;

use Herald\Parser\XML as XMLParser;
use Herald\Notice\Notice;
use Herald\Notice\PMD as PMDNotice;

class XML extends XMLParser
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
        if (!$this->document->count()) {
            return [];
        }

        $notices = [];

        foreach ($this->document->children() as $file) {
            $filePath = (string) $file->attributes()['name'];

            foreach ($file->children() as $item) {
                $attributes = $item->attributes();

                $notice = new PMDNotice;

                if ($attributes['priority'] > 1) {
                    $notice->setSeverity(Notice::SEVERITY_WARNING);
                }
                $notice->setFile($filePath);
                $notice->setLineStart((int) $attributes['beginline']);
                $notice->setLineEnd((int) $attributes['endline']);
                $notice->setDescription(trim((string) $item, "\n ."));

                if (isset($attributes['externalInfoUrl']) && (string) $attributes['externalInfoUrl'] !== '#') {
                    $notice->setExtraInfo((string) $attributes['externalInfoUrl']);
                }

                $notices[] = $notice;
            }
        }

        return $notices;
    }
}
