<?php

/*
 * This file is part of Dbup.
 *
 * (c) Masao Maeda <brt.river@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dbup\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Dbup\Exception\RuntimeException;

/**
 * @author Masao Maeda <brt.river@gmail.com>
 */
class StatusCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('status')
            ->setDescription('Display migration status')
            ->addOption(
                'ini',
                null,
                InputOption::VALUE_REQUIRED,
                'If set, the task uses it instead of default one'
            )
            ->setHelp('
The <info>dbup status</info> comand shows migration statuses.
If migration sql files were applied, display applied time and file names.
otherwise not applied, display <info>appending...</info>.
dbup checks whether sql files were applied or not by comparing the file names in <info>sql directory</info> and <info>applied directory</info>.
            ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication();
        $ini = $input->getOption('ini');
        if (!$ini) {
            $ini = $app->getIni();
        }
        if (!file_exists($ini)) {
            throw new RuntimeException($ini . ' does not exist.');
        }

        $app->setConfigFromIni($ini);

        $statuses = $app->getStatuses();

        $format = "%20s | %s";

        $lines[] = '<info>dbup migration status</info>';
        $lines[] = str_repeat('=', 80);
        $lines[] = sprintf($format, "Applied At", "migration sql file");
        $lines[] = str_repeat('-', 80);

        foreach($statuses as $status){
            $appliedAt = $status->appliedAt === '' ? "appending...": $status->appliedAt;
            $lines[] = sprintf($format, $appliedAt, $status->file->getFileName());
        }

        foreach($lines as $line){
            $output->writeln($line);
        }

    }
}
