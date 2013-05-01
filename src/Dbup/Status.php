<?php
/*
 * This file is part of Dbup.
 *
 * (c) Masao Maeda <brt.river@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Dbup;

class Status
{
    public $appliedAt = '';
    public $file;

    public function __construct($appliedAt, $file)
    {
        $this->appliedAt = $appliedAt;
        $this->file = $file;
    }
}