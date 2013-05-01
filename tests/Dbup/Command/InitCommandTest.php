<?php
namespace Dbup\Tests\Command;

use Dbup\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Dbup\Command\InitCommand;

class InitCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testInitializeDirsAndFiles()
    {
        /**
         * dirs and files to create.
         */
        $dirs = [
            __DIR__ . '/../../../sql',
            __DIR__ . '/../../../.dbup/applied',
            __DIR__ . '/../../../.dbup',
        ];
        $files = [
            __DIR__ . '/../../../sql/V1__sample_select.sql',
            __DIR__ . '/../../../.dbup/properties.ini',
        ];

        /**
         * cleaner the created files and dirs.
         */
        $clean = function() use($dirs, $files){
            foreach($files as $file){
                @unlink($file);
            }
            foreach($dirs as $dir){
                @rmdir($dir);
            }
        };

        $clean();

        $application = new Application();
        $application->add(new InitCommand());

        $command = $application->find('init');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        foreach($dirs as $dir) {
            assertThat(is_dir($dir), is(true));
        }
        foreach($files as $file) {
            assertThat(file_exists($file), is(true));
        }

        $clean();
    }
}