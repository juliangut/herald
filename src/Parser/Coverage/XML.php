<?php

namespace Herald\Parser\Coverage;

use Herald\Parser\XML as XMLParser;
use Herald\Notice\Notice;
use Herald\Notice\Coverage as CoverageNotice;

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
                if ($item->getName() !== 'line') {
                    continue;
                }

                $attributes = $item->attributes();
                $notice = new CoverageNotice;

                $notice->setFile($filePath);
                $notice->setLineStart((int) $attributes['num']);
                $notice->setLineEnd((int) $attributes['num']);
                $notice->setDescription('Number of tests: ' . (string) $attributes['count']);

                $notices[] = $notice;
            }
        }

        return $notices;
    }
}
