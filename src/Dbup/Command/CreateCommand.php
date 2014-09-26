<?php

namespace Dbup\Command;

use Dbup\Exception\RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends Command
{

    protected function configure ()
    {
        $this->setName('create')
            ->setDescription('Create a migration')
            ->addArgument('name', InputArgument::REQUIRED, 'Description of migration.')
            ->addOption('ini', null, InputOption::VALUE_OPTIONAL)
            ->setHelp('
The <info>dbup up</info> command create empty migration timestamped with name.

<info>$ php dbup create my_migration</info>

Create migration/file in the <info>./sql</info> directory with name <info>VYYYYMMDDHHMMSS__my_migration.sql</info>
            ');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $ini = $input->getOption('ini');
        $app = $this->getApplication();

        if (!$ini) {
            $ini = $app->getIni();
        }
        if (!file_exists($ini)) {
            throw new RuntimeException($ini . ' does not exist.');
        }

        $app->setConfigFromIni($ini);

        $name = $input->getArgument('name');
        $sqlPath = $app->sqlFilesDir;

        $version = date("YmdHis");
        $fileName = $sqlPath . "/" . "V{$version}" . "__{$name}.sql";

        $saved = file_put_contents($fileName, "");
        if (false === $saved) {
            throw new RuntimeException("<error>Cannot create migration '{$fileName}'</error>");
        }

        $output->writeln("Migration '{$fileName}' created.");
    }
}
