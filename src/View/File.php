<?php

namespace Herald\View;

use Twig_Environment;
use Herald\Report\File as ReportFile;

class File
{
    /**
     * @var \Twig_Environment
     */
    protected $templateManager;

    protected $outputDir;

    protected $templateFile = 'fileContent.twig';

    public function __construct(Twig_Environment $templateManager, $output)
    {
        $this->templateManager = $templateManager;
        $this->outputDir = $output;
    }

    public function render(ReportFile $file, $fileName)
    {
        $lines = $file->getAnnotatedContent();

        $rendered = $this->templateManager->render($this->templateFile, ['filename' => $fileName, 'lines' => $lines]);

        file_put_contents($this->outputDir . '/' . $fileName . '.html', $rendered);
    }
}
