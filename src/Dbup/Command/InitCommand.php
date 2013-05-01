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
class InitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Initialize dbup')
            ->setHelp('
The <info>dbup init</info> comand initialize dbup environment.
This create <info>.dbup/properties.ini</info> and <info>./sql</info> directory and <info>./.dbup/applied</info> directory if not exists.
            ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = './.dbup/properties.ini';
        $output->writeln('<info>create properties.ini:</info> ' . $path);
        if (file_exists($path)) {
            $output->writeln('<comment>  already exists. skipped</comment>');
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            if (false === mkdir($dir, 0777 - umask(), true)) {
                throw new RuntimeException('cannot make dir: ' . $dir);
            }
        }

        $properties =<<<EOL
;; pdo's constructor parameters
;; see also http://jp1.php.net/manual/en/pdo.construct.php
[pdo]
dsn = "mysql:dbname=demo;host=localhost"
user = "demo"
password = "demo"

;; pdo option
;; see also http://jp1.php.net/manual/en/pdo.construct.php
[pdo_options]


;; if set pathes, dbup will use this pathes instead of default ones.
[path]
;sql = /etc/dbup/sql
;applied = /etc/dbup/applied
EOL;

        if (false === file_put_contents($path, $properties)) {
            throw new RuntimeException('cannot make property file to ' . $path);
        }


        foreach(['./sql', './.dbup/applied'] as $dir){
            $output->writeln('<info>create directory</info> ' . $dir);
            if (!is_dir($dir)) {
                if (false === mkdir($dir, 0777 - umask(), true)) {
                    throw new RuntimeException('cannot make dir: ' . $dir);
                }
            } else {
                $output->writeln('<comment>  already exists. skipped.</comment> ');
            }
        }

        $sample =<<<EOL
-- sample sql
select 1+1;
EOL;

        $sampleSqlFilePath = './sql/V1__sample_select.sql';
        $output->writeln('<info>create sample sql migraion file:</info> ' . $sampleSqlFilePath);
        if (false === file_put_contents($sampleSqlFilePath, $sample)) {
            throw new RuntimeException('cannot make a sample sql file to ' . $sampleSqlFilePath);
        }

        $output->writeln('<info>done.</info>');
    }
}
