<?php

namespace Herald\Parser;

interface Parser
{
    /**
     * Loads file content
     *
     * @param string $file
     * @throws \RuntimeException
     */
    public function loadFile($file);

    /**
     * Retrieve list of analized notices
     *
     * @return \Herald\Notice\Notice[]
     */
    public function getNotices();
}
