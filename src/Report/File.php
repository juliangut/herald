<?php

namespace Herald\Report;

use Symfony\Component\Filesystem\Filesystem;
use Herald\Notice\Notice;

class File
{
    protected $path;

    /**
     * List of parsed informational notices
     *
     * @var \Herald\Notice\Notice[]
     */
    protected $infos = [];

    /**
     * List of parsed warning notices
     *
     * @var \Herald\Notice\Notice[]
     */
    protected $warnings = [];

    /**
     * List of parsed error notices
     *
     * @var \Herald\Notice\Notice[]
     */
    protected $errors = [];

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->setPath($path);
    }

    /**
     * Set file absolute path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $filesystem = new Filesystem;

        if (!$filesystem->isAbsolutePath($path)) {
            $path = realpath($path);
        }

        if (!$filesystem->exists($path)) {
            throw new \InvalidArgumentException(sprintf('File "%s" does not exist', $path));
        }

        $this->path = $path;
    }

    /**
     * Get file path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Add notice
     *
     * @param \Herald\Notice\Notice $notice
     */
    public function addNotice(Notice $notice)
    {
        if ($notice->getFile() !== $this->path) {
            return;
        }

        if ($notice->getSeverity() === Notice::SEVERITY_INFO) {
            $this->infos[] = $notice;
        }
        if ($notice->getSeverity() === Notice::SEVERITY_WARNING) {
            $this->warnings[] = $notice;
        }
        if ($notice->getSeverity() === Notice::SEVERITY_ERROR) {
            $this->errors[] = $notice;
        }
    }

    /**
     * Retrieve list of parsed informational notices
     *
     * @return array
     */
    public function getInfos()
    {
        return $this->infos;
    }

    /**
     * Retrieve list of parsed warning notices
     *
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Retrieve list of parsed error notices
     *
     * @return array
     */
    public function getOnlyErrors()
    {
        return $this->errors;
    }

    /**
     * Retrieve list of parsed warning and error notices
     *
     * @return array
     */
    public function getErrors()
    {
        return array_merge($this->getWarnings(), $this->getOnlyErrors());
    }

    /**
     * Get file content with associated notices
     *
     * @return array
     */
    public function getAnnotatedLines(
        $severity = Notice::SEVERITY_INFO | NOTICE::SEVERITY_WARNING | Notice::SEVERITY_ERROR
    ) {
        $notices = [];
        if ($severity & Notice::SEVERITY_INFO) {
            $notices = array_merge($notices, $this->getInfos());
        }
        if ($severity & Notice::SEVERITY_WARNING) {
            $notices = array_merge($notices, $this->getWarnings());
        }
        if ($severity & Notice::SEVERITY_ERROR) {
            $notices = array_merge($notices, $this->getOnlyErrors());
        }

        usort(
            $notices,
            function ($notice1, $notice2) {
                if ($notice1->getLineStart() == $notice2->getLineStart()) {
                    return 0;
                }

                return ($notice1->getLineStart() < $notice2->getLineStart()) ? -1 : 1;
            }
        );

        $getLineNotices = function ($line) use ($notices) {
            $lineNotices = [];

            foreach ($notices as $notice) {
                if ($notice->getLineStart() === $line) {
                    $lineNotices[] = $notice;

                    break;
                }

                if ($notice->getLineStart() > $line) {
                    break;
                }
            }

            return $lineNotices;
        };

        $fileContent = explode("\n", file_get_contents($this->path));

        array_walk(
            $fileContent,
            function (&$line, $index) use ($getLineNotices) {
                $line = [
                    'line'    => $line,
                    'notices' => $getLineNotices($index),
                ];
            }
        );

        return $fileContent;
    }
}
