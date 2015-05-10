<?php

namespace Herald\Parser;

abstract class JSON implements Parser
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
        $document = json_decode(file_get_contents($file));

        if (json_last_error() === JSON_ERROR_NONE) {
            $this->document = $document;
        } else {
            throw new \RuntimeException(sprintf('File "%s" is not a valid JSON file', $file));
        }
    }
}
