Dbup, a simple PHP migration tool
==================================

[![Build Status](https://travis-ci.org/brtriver/dbup.png)](https://travis-ci.org/brtriver/dbup)

Dbup is a simple migration tool for PHP.

- You have only to download [`dbup.phar`][1].
- Dbup has only `up` command. Dbup does not have `down` command.
- Dbup use just a plain old sql, so you don't have to learn ORM nor DSL. You write sql file and just call `up` command.
- Dbup use just PDO class to migrate.
- Dbup doesn't need the table in a database to migrate.

Applied migration sql files are copied to `.dbup/applied` directory.
If a same file exists both `sql` and `.dbup/applied` directory, `up` command ignores this sql file.

simple...simple..simple..

Requirements
------------

Dbup works with PHP 5.4.0 or later.

Install
--------

Installing Dbup is as easy as it can get. Download the [`dbup.phar`][1] and run `init`,
then `.dbup` and `sql` directory are created and set a sample `properties.ini` file and sqlfile.

    php dbup.phar init

change the database config in `.dbup/properties.ini`.

    [pdo]
    dsn = "mysql:dbname=testdatabase;host=localhost"
    user = "testuser"
    password = "testpassword"

see also http://www.php.net/manual/en/pdo.construct.php

You can also assign environment variables to your database configuration file. Dbup reads `DBUP_` prefixed environment variables if the names are placed in .ini file with surrounded '%%'. For example, `user` parameter of the following ini will be replaced to a value of the environment variable `DBUP_USERNAME` if it is defined.

    [pdo]
    user = "%%DBUP_USERNAME%%"

Naming
------

You have to name a sql file like below:

    V[version_number]__[description].sql

`V` is prefix. the separator is `__` (two underscores). Suffix is `.sql`

Usage
-----

List all commands.

    php dbup.phar

You have to write a sql file to `sql` directory.


Show status.

    php dbup.phar status

    dbup migration status
    ================================================================================
              Applied At | migration sql file
    --------------------------------------------------------------------------------
     2013-05-01 22:37:32 | V1__sample_select.sql
            appending... | V2__sample.sql
            appending... | V3__sample.sql
            appending... | V20__sample.sql
            appending... | V100__sample.sql

Up database after writing a new sql file.

    php dbup.phar up

that's all.

License
-------

Dbup is licensed under the MIT license.

[1]: https://raw.github.com/brtriver/dbup/master/dbup.phar
