<?php

namespace Herald\Parser;

abstract class XML implements Parser
{
    /**
     * Loaded file content
     *
     * @var \SimpleXMLElement
     */
    protected $document;

    /**
     * {@inheritDoc}
     */
    public function loadFile($file)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->validateOnParse = true;
        if (@$dom->load($file)) {
            $document = simplexml_import_dom($dom);

            if (!$document instanceof \SimpleXMLElement) {
                throw new \RuntimeException(sprintf('File "%s" is not a valid XML file', $file));
            }

            $this->document = $document;
        } else {
            throw new \RuntimeException(sprintf('File "%s" is not a valid XML file', $file));
        }
    }
}
