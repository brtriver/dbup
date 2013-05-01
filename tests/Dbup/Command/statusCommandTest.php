<?php
namespace Dbup\Tests\Command;

use Dbup\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Dbup\Command\StatusCommand;

class StatusCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testSpecificPropertiesIni()
    {
        $application = new Application();
        $application->add(new StatusCommand());

        $command = $application->find('status');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(),
                '--ini' => __DIR__ . '/../.dbup/properties.ini.test',
            ]);

        assertThat($commandTester->getDisplay(), is(containsString('appending... | V12__sample12_select.sql')));
    }

    /**
     * @expectedException Dbup\Exception\RuntimeException
     */
    public function testCatchExceptionNonExistIni()
    {
        $application = new Application();
        $application->add(new StatusCommand());

        $command = $application->find('status');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '--ini' => 'notfound.ini']);
    }
}