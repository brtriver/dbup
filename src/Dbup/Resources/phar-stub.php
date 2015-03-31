#!/usr/bin/env php
<?php
/*
 * This file is part of Dbup.
 *
 * (c) Masao Maeda <brt.river@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
Phar::mapPhar('dbup.phar');
require_once 'phar://dbup.phar/vendor/autoload.php';
use Dbup\Application;
$application = new Application();
$application->run();

__HALT_COMPILER();