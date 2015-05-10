<?php

namespace Herald\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Herald\View\View as ViewManager;

/**
 * Base Herald command
 */
class RunCommand extends Command
{
    protected $reports = [
        'pmd'        => 'Herald\\Report\\PMD',
        'checkstyle' => 'Herald\\Report\\Checkstyle',
        'cpd'        => 'Herald\\Report\\CPD',
        'crap'       => 'Herald\\Report\\CRAP',
        'coverage'   => 'Herald\\Report\\Coverage',
    ];

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('herald:run')
            ->setDescription('Combine report files')
            ->setDefinition([
                new InputOption(
                    'reports',
                    'reports',
                    InputOption::VALUE_REQUIRED,
                    'Path to report files'
                ),
                new InputOption(
                    'output',
                    'output',
                    InputOption::VALUE_REQUIRED,
                    'Path to output herald files'
                ),
                new InputOption(
                    'disable',
                    'disable',
                    InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                    'List of disabled reports'
                ),
            ])
            ->setHelp(
                'Combine Quality Assurance tools reports for better view'
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filesystem = new Filesystem;

        $this->verifyConstraints($input, $filesystem);

        $reportsPath = $input->getOption('reports');
        $outputPath = $input->getOption('output');

        $enabledReports = $this->getEnabledReports($input->getOption('disable'));

        $activeReports = [];

        $iteratorFactory  = new \File_Iterator_Factory();
        $iterator = $iteratorFactory->getFileIterator($reportsPath);

        foreach ($iterator as $file) {
            if ($file->isDir() || !$file->isReadable()) {
                continue;
            }

            foreach ($enabledReports as $report) {
                if ($report->canHandleFile($file->getFilename())
                    && $report->loadFile($file->getRealPath())
                ) {
                    $activeReports[] = $report;
                }
            }
        }

        if (empty($activeReports)) {
            throw new \RuntimeException(sprintf('No reports found on "%s"', $reportsPath));
        }

        if ($filesystem->exists($outputPath)) {
            $filesystem->remove($outputPath);
        }
        $filesystem->mkdir($outputPath);

        $view = new ViewManager($outputPath);

        foreach ($activeReports as $report) {
            $view->addReport($report);
        }

        $view->render();
    }

    /**
     * Verify input option constraints
     *
     * @param Symfony\Component\Console\Input\InputInterface $input
     * @throws \InvalidArgumentException
     */
    protected function verifyConstraints(InputInterface $input, Filesystem $filesystem)
    {
        if (!$input->getOption('reports')) {
            throw new \InvalidArgumentException('Missing reports argument');
        } elseif (!$filesystem->exists($input->getOption('reports')) || !is_dir($input->getOption('reports'))) {
            throw new \InvalidArgumentException('Reports argument must be an existing directory');
        }

        if (!$input->getOption('output')) {
            throw new \InvalidArgumentException('Missing output argument');
        } elseif ($filesystem->exists($input->getOption('output')) && !is_dir($input->getOption('output'))) {
            throw new \InvalidArgumentException('Output argument must be a directory');
        }
    }

    /**
     * Get enabled reports
     *
     * @param array $disabledReports
     * @throws \InvalidArgumentException
     * @return array
     */
    protected function getEnabledReports(array $disabledReports)
    {
        $disabledReports = array_map(
            function($report) {
                if (!array_key_exists($report, $this->reports)) {
                    throw new \InvalidArgumentException(sprintf('Report "%s" to be disabled does not exist', $report));
                }

                return $this->reports[$report];
            },
            $disabledReports
        );

        $enabledReports = array_diff(array_values($this->reports), $disabledReports);

        return array_map(
            function($report) {
                return new $report;
            },
            $enabledReports
        );
    }
}
