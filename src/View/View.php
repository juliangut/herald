<?php

namespace Herald\View;

use Twig_Loader_Filesystem;
use Twig_Environment;
use Herald\Report\Report;
use Herald\Report\File as FileReport;
use Symfony\Component\Filesystem\Filesystem;

class View
{
    protected $templateManager;

    protected $templateDir;

    protected $outputDir;

    protected $filesystem;

    protected $reports = [];

    public function __construct($outputDir)
    {
        $this->outputDir = $outputDir;

        $this->templateDir = __DIR__ . '/Templates';

        $loader = new Twig_Loader_Filesystem($this->templateDir);
        $this->templateManager = new Twig_Environment($loader);

        $this->filesystem = new Filesystem;
    }

    public function addReport(Report $report)
    {
        $this->reports[] = $report;
    }

    public function render()
    {
        $this->recurseCopy($this->templateDir . '/assets', $this->outputDir . '/assets');

        $this->renderCodeReport();
    }

    protected function renderCodeReport()
    {
        $codeOutputDir = $this->outputDir . '/code';

        if (!$this->filesystem->exists($codeOutputDir)) {
            $this->filesystem->mkdir($codeOutputDir);
        }

        $files = [];
        foreach ($this->reports as $report) {
            $files = array_merge($files, $report->getFiles());
        }

        $reportFiles = array_keys($files);
        $basePath = $this->commonPath($reportFiles);
        $reportFiles = array_map(
            function ($filePath) use ($basePath) {
                return str_replace($basePath, '', $filePath);
            },
            $reportFiles
        );

        $rendered = $this->templateManager->render(
            'codeFiles.twig',
            [
                'title' => 'Code files',
                'files' => $reportFiles,
            ]
        );

        file_put_contents($this->outputDir . '/index.html', $rendered);

        foreach ($files as $filePath => $file) {
            $filePath  = str_replace($basePath, '', $filePath);
            $fileRoute = $codeOutputDir . '/' . str_replace('/', '_', $filePath);

            $rendered = $this->templateManager->render(
                'codeFile.twig',
                [
                    'title' => $filePath,
                    'lines' => json_encode(implode("\n", array_map(
                        function ($el) {
                            return $el['line'];
                        },
                        $file->getAnnotatedLines()
                    ))),
                ]
            );

            file_put_contents($fileRoute . '.json', $rendered);
        }
    }

    protected function commonPath($fileNames)
    {
        $sortStrlen = function($a, $b) {
            if (strlen($a) == strlen($b)) {
                return strcmp($a, $b);
            }

            return (strlen($a) < strlen($b)) ? -1 : 1;
        };
        usort($fileNames, $sortStrlen);

        $commonSubstring = array();
        $shortestString = str_split(array_shift($fileNames));

        while (sizeof($shortestString)) {
            array_unshift($commonSubstring, '');
            foreach ($shortestString as $charcter) {
                foreach ($fileNames as $fileName) {
                    if (!strstr($fileName, $commonSubstring[0] . $charcter)) {
                        break 2;
                    }
                }

                $commonSubstring[0] .= $charcter;
            }

            array_shift($shortestString);
        }

        usort($commonSubstring, $sortStrlen);

        return array_pop($commonSubstring);
    }

    protected function recurseCopy($src, $dst)
    {
        $this->filesystem->mkdir($dst);

        $dir = opendir($src);

        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    $this->filesystem->copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }

        closedir($dir);
    }
}
