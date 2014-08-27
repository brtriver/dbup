<?php
namespace Dbup\Tests;

use Dbup\Application;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

Class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public $app;
    public $pdo;

    public function setUP()
    {
        $this->app = \Phake::partialMock('Dbup\Application');
        $this->pdo = \Phake::mock('Dbup\Database\PdoDatabase');
        $this->dbh = \Phake::mock('\Pdo');
        \Phake::when($this->pdo)->connection(\Phake::anyParameters())->thenReturn($this->dbh);
        $this->stmt = \Phake::mock('\PDOStatement');
        \Phake::when($this->dbh)->prepare(\Phake::anyParameters())->thenReturn($this->stmt);
    }

    public function testSetPropertiesWhenInstanceIsMade()
    {
        assertThat($this->app->sqlFilesDir, is('./sql'));
        assertThat($this->app->appliedFilesDir, is('./.dbup/applied'));
    }

    public function testGetIniFilePath()
    {
        assertThat($this->app->getIni(), is('./.dbup/properties.ini'));
    }

    public function testParseIniFileReplaceVariables()
    {
        $_SERVER['DBUP_TEST_DBMS'] = 'replaced_dbms';
        $_SERVER['DBUP_TEST_USER'] = 'replaced_user';
        $_SERVER['DBUP_TEST_HOST'] = 'replaced_host';
        $_SERVER['OTHER_PREFIX_DBUP_TEST_PASSWORD'] = 'replaced_password';

        $ini = __DIR__ . '/.dbup/properties.ini.replace';
        $parsed = $this->app->parseIniFile($ini)['pdo'];

        assertThat($parsed['dsn'], is('replaced_dbms:dbname=replaced_user_replaced_host_%%DBUP_TEST_NOT_REPLACED%%;host=replaced_host'));
        assertThat($parsed['user'], is('%DBUP_TEST_USER%'));
        assertThat($parsed['password'], is('%%OTHER_PREFIX_DBUP_TEST_PASSWORD%%'));
    }

    public function testSetConfigFromIni()
    {
        $ini = __DIR__ . '/.dbup/properties.ini';
        $this->app->setConfigFromIni($ini);

        assertThat($this->app->sqlFilesDir, is('/etc/dbup/sql'));
        assertThat($this->app->appliedFilesDir, is('/etc/dbup/applied'));
        \Phake::verify($this->app)->createPdo('mysql:dbname=testdatabase;host=localhost', 'testuser', 'testpassword', ['\PDO::MYSQL_ATTR_LOCAL_INFILE' => 1]);
    }

    /**
     * @expectedException Dbup\Exception\RuntimeException
     */
    public function testCatchExceptionSetConfigFromEmptyIni()
    {
        $ini = __DIR__ . '/.dbup/properties.ini.empty';
        $this->app->setConfigFromIni($ini);
    }

    public function testSetConfigFromMinIni()
    {
        $ini = __DIR__ . '/.dbup/properties.ini.min';
        $this->app->setConfigFromIni($ini);

        \Phake::verify($this->app)->createPdo('mysql:dbname=testdatabase;host=localhost', '', '', []);
    }

    public function testGetSqlFileByName()
    {
        $this->app->sqlFilesDir = __DIR__ . '/sql';
        $file = $this->app->getSqlFileByName('V1__sample_select.sql');
        assertThat($file->getFileName(), is('V1__sample_select.sql'));
    }

    /**
     * @expectedException Dbup\Exception\RuntimeException
     */
    public function testCatchExceptionWhenNotFoundSqlFileByName()
    {
        $this->app->sqlFilesDir = __DIR__ . '/sql';
        $this->app->getSqlFileByName('hoge');
    }

    public function testGetStatuses()
    {
        $this->app->sqlFilesDir = __DIR__ . '/sql';
        $this->app->appliedFilesDir = __DIR__ . '/.dbup/applied';

        $statuses = $this->app->getStatuses();

        assertThat(count($statuses), is(3));
        assertThat($statuses[0]->appliedAt, is(not('')));
        assertThat($statuses[0]->file->getFileName(), is('V1__sample_select.sql'));
        assertThat($statuses[1]->appliedAt, is(''));
        assertThat($statuses[1]->file->getFileName(), is('V3__sample3_select.sql'));
        assertThat($statuses[2]->appliedAt, is(''));
        assertThat($statuses[2]->file->getFileName(), is('V12__sample12_select.sql'));
    }

    public function testGetUpCandidates()
    {
        $this->app->sqlFilesDir = __DIR__ . '/sql';
        $this->app->appliedFilesDir = __DIR__ . '/.dbup/applied';

        $candidates = $this->app->getUpCandidates();

        assertThat(count($candidates), is(2));
        assertThat($candidates[0]->file->getFileName(), is('V3__sample3_select.sql'));
        assertThat($candidates[1]->file->getFileName(), is('V12__sample12_select.sql'));
    }

    /**
     * @expectedException Dbup\Exception\RuntimeException
     */
    public function testCatchExceptionCopyToAppliedDir()
    {
        $this->app->appliedFilesDir = __DIR__ . '/nondir';
        $file = new \SplFileInfo(__DIR__ . '/samples/plural.sql');
        $this->app->copyToAppliedDir($file);
    }

    public function testCopyToAppliedDir()
    {
        @unlink(__DIR__ . '/.dbup/applied/single.sql');

        $this->app->appliedFilesDir = __DIR__ . '/.dbup/applied';
        $file = new \SplFileInfo(__DIR__ . '/samples/single.sql');
        $this->app->copyToAppliedDir($file);

        assertThat(file_exists(__DIR__ . '/.dbup/applied/single.sql'), is(true));

        @unlink(__DIR__ . '/.dbup/applied/single.sql');
    }

    public function testUpWithSingleStatementSqlFile()
    {
        $this->app->appliedFilesDir = __DIR__ . '/.dbup/applied';
                $this->app->pdo = $this->pdo;
        $file = new \SplFileInfo(__DIR__ . '/samples/single.sql');

        $this->app->up($file);

        \Phake::verify($this->dbh, \Phake::times(1))->prepare('select 1+1');

        @unlink(__DIR__ . '/.dbup/applied/single.sql');
    }

    public function testUpWithPluralStatementsSqlFile()
    {
        $this->app->appliedFilesDir = __DIR__ . '/.dbup/applied';
        $this->app->pdo = $this->pdo;
        $file = new \SplFileInfo(__DIR__ . '/samples/plural.sql');

        $this->app->up($file);

        \Phake::verify($this->dbh, \Phake::times(1))->prepare('select 1+1');
        \Phake::verify($this->dbh, \Phake::times(1))->prepare('select 2+2');

        @unlink(__DIR__ . '/.dbup/applied/plural.sql');
    }

    /**
     * @expectedException Dbup\Exception\RuntimeException
     */
    public function testCatchExceptionWhenUp()
    {
        $this->app->pdo = $this->pdo;
        $file = new \SplFileInfo(__DIR__ . '/samples/single.sql');

        \Phake::when($this->dbh)->prepare(\Phake::anyParameters())->thenThrow(new \PDOException);

        $this->app->up($file);
    }

    /**
     * issue #1
     */
    public function testUpWithSingleStatementWithEmptyLineSqlFile()
    {
        $this->app->appliedFilesDir = __DIR__ . '/.dbup/applied';
        $this->app->pdo = $this->pdo;
        $file = new \SplFileInfo(__DIR__ . '/samples/singleWithEmpty.sql');

        $this->app->up($file);

        \Phake::verify($this->dbh, \Phake::times(1))->prepare(\Phake::anyParameters());

        @unlink(__DIR__ . '/.dbup/applied/single.sql');
    }
}
