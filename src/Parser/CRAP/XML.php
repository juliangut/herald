<?php

namespace Herald\Parser\CRAP;

use Herald\Parser\XML as XMLParser;
use Herald\Notice\Notice;
use Herald\Notice\CRAP as CRAPNotice;

class XML extends XMLParser
{
    /**
     * {@inheritDoc}
     */
    public function loadFile($file)
    {
        parent::loadFile($file);

        if (!$this->document->count()) {
            return;
        }

        $project = $this->document->children()[0];
        if ($project->getName() !== 'project') {
            throw new \RuntimeException(sprintf('File "%s" is not a valid Coverage file', $file));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getNotices()
    {
        if (!$this->document->count()) {
            return [];
        }

        $project = $this->document->children()[0];

        $notices = [];

        foreach ($project->children() as $component) {
            if ($component->getName() === 'metrics') {
                continue;
            }

            $notices = array_merge($notices, $this->parsePackage($component));
        }

        return $notices;
    }

    /**
     * Parse each package in the report
     *
     * @param \SimpleXMLElement $package
     * @return array
     */
    protected function parsePackage(\SimpleXMLElement $package)
    {
        $notices = [];

        foreach ($package->children() as $file) {
            $filePath = (string) $file->attributes()['name'];

            foreach ($file->children() as $item) {
                if (in_array($item->getName(), ['class', 'metrics'])) {
                    continue;
                }

                $attributes = $item->attributes();

                if (isset($attributes['crap'])) {
                    $notice = new CRAPNotice;

                    $notice->setFile($filePath);
                    $notice->setLineStart((int) $attributes['num']);
                    $notice->setLineEnd((int) $attributes['num']);
                    $notice->setDescription('CRAP: ' . (string) $attributes['crap']);

                    $notices[] = $notice;
                }
            }
        }

        return $notices;
    }
}
