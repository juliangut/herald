<?php

namespace Herald\Parser;

abstract class CSV implements Parser
{
    /**
     * Loaded file content
     *
     * @var array
     */
    protected $document;

    /**
     * {@inheritDoc}
     */
    public function loadFile($file)
    {
        $fileHandler = fopen($file, 'r');

        if ($fileHandler !== false) {
            $this->document = [];

            while (!feof($fileHandler)) {
                $this->document[] = fgetcsv($fileHandler, 1024);
            }
            fclose($fileHandler);
        }

        if ($this->document === null) {
            throw new \RuntimeException(sprintf('File "%s" is not a valid CSV file', $file));
        }
    }
}
