<?php

namespace Herald\Parser\CPD;

use Herald\Parser\XML as XMLParser;
use Herald\Notice\Notice;
use Herald\Notice\CPD as CPDNotice;

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

        foreach ($this->document->children() as $duplication) {
            $originalItem = null;
            $copiedLines = [];

            foreach ($duplication->children() as $item) {
                if ($item->getName() !== 'file') {
                    continue;
                }

                if ($originalItem === null) {
                    $originalItem = $item;

                    continue;
                }

                $attributes = $item->attributes();

                $copiedLines[] = sprintf(
                    '%s:%s',
                    (string) $attributes['path'],
                    (int) $attributes['line']
                );
            }

            $duplicationAttributes = $duplication->attributes();
            $originalItemAttributes = $originalItem->attributes();

            $notice = new CPDNotice;

            $notice->setFile((string) $originalItemAttributes['path']);
            $notice->setLineStart((int) $originalItemAttributes['line']);
            $notice->setLineEnd((int) $originalItemAttributes['line'] + (int) $duplicationAttributes['lines']);
            $notice->setDescription(sprintf(
                "Copied from:\n%s",
                implode("\n", $copiedLines)
            ));

            $notices[] = $notice;
        }

        return $notices;
    }
}
