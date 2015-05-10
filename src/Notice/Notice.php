<?php

namespace Herald\Notice;

abstract class Notice
{
    const SEVERITY_INFO    = 1;
    const SEVERITY_WARNING = 2;
    const SEVERITY_ERROR   = 4;

    /**
     * Report type
     *
     * @var string
     */
    protected $report;

    /**
     * Severity level
     *
     * @var int
     */
    protected $severity;

    /**
     * File of notice
     *
     * @var string
     */
    protected $file = '';

    /**
     * Notice start line
     *
     * @var int
     */
    protected $lineStart;

    /**
     * Notice end line
     *
     * @var int
     */
    protected $lineEnd;

    /**
     * Notice description
     *
     * @var int
     */
    protected $description = '';

    /**
     * Notice extra information
     *
     * @var string
     */
    protected $extraInfo = '';

    /**
     * Get report type
     *
     * @return string
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Set notice severity level
     *
     * @param int $severity
     */
    public function setSeverity($severity = self::SEVERITY_ERROR)
    {
        $this->severity = $severity;
    }

    /**
     * Get notice severity level
     *
     * @return int
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * Set file of notice
     *
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Get file of notice
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set notice start line
     *
     * @param int $lineStart
     */
    public function setLineStart($lineStart)
    {
        $this->lineStart = max(0, $lineStart);
    }

    /**
     * Get notice start line
     *
     * @return int
     */
    public function getLineStart()
    {
        return $this->lineStart;
    }

    /**
     * Set notice end line
     *
     * @param int $lineEnd
     */
    public function setLineEnd($lineEnd)
    {
        $this->lineEnd = max(0, $lineEnd);
    }

    /**
     * Get notice end line
     *
     * @return int
     */
    public function getLineEnd()
    {
        return $this->lineEnd;
    }

    /**
     * Set notice description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get notice description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set notice extra information
     *
     * @param string $extraInfo
     */
    public function setExtraInfo($extraInfo)
    {
        $this->extraInfo = $extraInfo;
    }

    /**
     * Get notice extra infomration
     *
     * @return string
     */
    public function getExtraInfo()
    {
        return $this->extraInfo;
    }
}
