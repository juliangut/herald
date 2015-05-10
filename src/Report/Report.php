<?php

namespace Herald\Report;

use Herald\Notice\Notice;

abstract class Report
{
    /**
     * Report name
     *
     * @var string
     */
    protected $name = '';

    /**
     * List of available report file parsers
     * Needs to be filled on every type of report:
     *
     * $reportParsers = [
     *     'report_file_name' => 'parser_class',
     * ];
     *
     * @var array
     */
    protected $reportParsers = [];

    /**
     * Selected report file parser
     */
    protected $parser;

    /**
     * Full list of parsed notices
     *
     * @var \Herald\Notice\Notice[]
     */
    protected $notices;

    /**
     * List of parsed files
     *
     * @var \Herald\Report\File[]
     */
    protected $files;

    /**
     * Returns report name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Verifies if a file can be handled
     *
     * @param string $fileName
     * @return bool
     */
    public function canHandleFile($fileName)
    {
        return $this->parser === null && array_key_exists($fileName, $this->reportParsers);
    }

    /**
     * Load a file
     *
     * @param string $filePath
     * @return bool
     */
    public function loadFile($filePath)
    {
        if (!$this->canHandleFile(basename($filePath))) {
            return false;
        }

        try {
            $parser = new $this->reportParsers[basename($filePath)];

            $parser->loadFile($filePath);

            $this->parser = $parser;

            return true;
        } catch (\RuntimeException $exception) {
        }

        return false;
    }

    /**
     * Retrieve list of parsed notices
     *
     * @return array
     */
    public function getNotices()
    {
        if ($this->notices === null) {
            $this->extractNotices();
        }

        return $this->notices;
    }

    /**
     * Retrieve list of analized files
     *
     * @return array
     */
    public function getFiles()
    {
        if ($this->notices === null) {
            $this->extractNotices();
        }

        return $this->files;
    }

    /**
     * Extract parsed errors
     */
    protected function extractNotices()
    {
        $this->notices = $this->parser->getNotices();
        $this->files    = [];

        foreach ($this->notices as $notice) {
            $filePath = $notice->getFile();

            if (!array_key_exists($filePath, $this->files)) {
                $this->files[$filePath] = new File($filePath);
            }

            $this->files[$filePath]->addNotice($notice);
        }

        ksort($this->files, SORT_NATURAL);
    }
}
