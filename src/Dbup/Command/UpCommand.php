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
class UpCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('up')
            ->setDescription('Update the database')
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                'if set the file name, execute specific sql file only'
            )
            ->addOption(
                'ini',
                null,
                InputOption::VALUE_REQUIRED,
                'If set, the task uses it instead of default one'
            )
            ->addOption(
                'dry-run',
                'd',
                InputOption::VALUE_NONE,
                'If set, the task runs as dry run mode'
            )
            ->setHelp('
The <info>dbup up</info> comand run all migrations to update.
If you want to run specific script, set argument of the file name like below:

<info>$ php dbup.phar up v100__create_user_table.sql</info>

By the default, dbup search migration files in <info>./sql</info> directory.
If you want to change it, you have to set your own properties.ini with <info>--ini</info>.
You can see details in the [path] section of the properties.ini.

<info>$ php dbup.phar up --ini=./tmp/properties.ini</info>

If you run with <info>--dry-run</info>, this command displays to be applied sql only.

<info>$ php dbup.phar up --dry-run</info>
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

        $isDryRun = $input->getOption('dry-run');
        if ($isDryRun) {
            $output->writeln('<error>now up is dry-run mode (--dry-run), so display only.</error>');
        }

        $app->setConfigFromIni($ini);
        if ($fileName = $input->getArgument('file')) {
            $file = $app->getSqlFileByName($fileName);
            $output->writeln('<info>applied specific sql file :</info>' . $file->getFileName());
            if (!$isDryRun) {
                $app->up($file);
            }
            $sql =file_get_contents($file->getPathName());
            $output->writeln(<<<EOL
<comment>executed sql:</comment>
$sql
EOL
            );
        } else {
            $output->writeln('<info>applied all candidates</info>');
            $this->upAllUnAppliedFiles($app, $output, $isDryRun);
        }

        $output->writeln('<info>success to up.</info>');

    }

    protected function upAllUnAppliedFiles($app, OutputInterface $output, $isDryRun)
    {
        $statuses = $app->getUpCandidates();
        foreach ($statuses as $status) {
            $output->writeln('<info>applied :</info>' . $status->file->getFileName());
            if (!$isDryRun) {
                $app->up($status->file);
            }
            $sql =file_get_contents($status->file->getPathName());
            $output->writeln(<<<EOL
<comment>executed sql:</comment>
$sql
EOL
            );
        }

    }
}
