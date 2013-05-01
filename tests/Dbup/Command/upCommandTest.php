<?php
namespace Dbup\Tests\Command;

use Dbup\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Dbup\Command\UpCommand;

class UpCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testDryRunMode()
    {
        $application = \Phake::partialMock('Dbup\Application');
        $application->add(new UpCommand());

        $command = $application->find('up');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(),
                '--ini' => __DIR__ . '/../.dbup/properties.ini.test',
                '--dry-run' => true,
            ]);
        assertThat($commandTester->getDisplay(), is(containsString('now up is dry-run mode (--dry-run), so display only')));
        \Phake::verify($application, \Phake::times(0))->up(\Phake::anyParameters());

    }

    public function testSomeUp()
    {
        $application = \Phake::partialMock('Dbup\Application');
        /** want not to run, so change up method to mock */
        \Phake::when($application)->up(\Phake::anyParameters())->thenReturn(null);

        $application->add(new UpCommand());

        $command = $application->find('up');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(),
                '--ini' => __DIR__ . '/../.dbup/properties.ini.test',
            ]);

        \Phake::verify($application, \Phake::times(2))->up(\Phake::anyParameters());
    }

    public function testSpecificSqlFile()
    {
        $application = \Phake::partialMock('Dbup\Application');
        /** want not to run, so change up method to mock */
        \Phake::when($application)->up(\Phake::anyParameters())->thenReturn(null);

        $application->add(new UpCommand());

        $command = $application->find('up');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(),
                '--ini' => __DIR__ . '/../.dbup/properties.ini.test',
                'file' => 'V12__sample12_select.sql',
            ]);

        \Phake::verify($application, \Phake::times(1))->up(\Phake::anyParameters());
    }

}