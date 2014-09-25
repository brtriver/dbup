<?php
namespace Dbup\Tests\Command;

use Dbup\Application;
use Dbup\Command\CreateCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Dbup\Command\InitCommand;

class CreateCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $application = \Phake::partialMock('Dbup\Application');
        $application->add(new CreateCommand());

        $command = $application->find('create');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'name' => 'foo',
                '--ini' => __DIR__ . '/../.dbup/properties.ini.test',
            ]
        );

        $display = $commandTester->getDisplay();
        assertThat($display, is(containsString('created')));

        preg_match('/\'(.+)\'/', $display, $matches);
        assertThat(1, count($matches));

        $migration = str_replace("'", "", $matches[0]);
        unlink(__DIR__ . '/../../../' . $migration);
    }
}
