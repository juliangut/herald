<?php

namespace Herald\Parser\Checkstyle;

use Herald\Parser\XML as XMLParser;
use Herald\Notice\Notice;
use Herald\Notice\Checkstyle as CheckstyleNotice;

class XML extends XMLParser
{
    const TYPE_CHECKSTYLE = 0;
    const TYPE_PHPCS      = 2;

    /**
     * Checkstyle type
     *
     * @var int
     */
    protected $type;

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

        $this->type = strtolower($this->document->getName()) === 'checkstyle'
            ? self::TYPE_CHECKSTYLE
            : self::TYPE_PHPCS;

        foreach ($this->document->children() as $file) {
            $filePath = (string) $file->attributes()['name'];

            foreach ($file->children() as $item) {
                $attributes = $item->attributes();

                $notice = new CheckstyleNotice;

                switch ($this->type) {
                    case self::TYPE_PHPCS:
                        $notice->setSeverity($item->getName() === 'error'
                            ? Notice::SEVERITY_ERROR
                            : Notice::SEVERITY_WARNING);
                        break;

                    case self::TYPE_CHECKSTYLE:
                    default:
                        $notice->setSeverity((string) $attributes['severity'] === 'error'
                            ? Notice::SEVERITY_ERROR
                            : Notice::SEVERITY_WARNING);
                }

                $notice->setFile($filePath);
                $notice->setLineStart((int) $attributes['line']);
                $notice->setLineEnd((int) $attributes['line']);
                $notice->setDescription((string) $attributes['message']);

                if (isset($attributes['fixable']) && (bool) $attributes['fixable']) {
                    $notice->setExtraInfo('Can be fixed with PHP_CodeSniffer phpcbf tool');
                }

                $notices[] = $notice;
            }
        }

        return $notices;
    }
}
