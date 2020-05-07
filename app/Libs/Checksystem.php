<?php

declare (strict_types = 1);

namespace App\Libs;

class Checksystem
{
    public function check()
    {
        $this->checksystem();
        $this->checkbredis();
        $this->checkphpversion();
        $this->checkswooleversion();
        $this->checkbcmath();
    }

    public function checksystem()
    {
        if (strtoupper(PHP_OS) == 'LINUX') {
            EchoLog('The current system is: Linux', null, true);
        } elseif (strpos(strtoupper(PHP_OS), 'WIN') != false) {
            EchoLog('The current system is: Windows', null, true);
        }
    }

    public function checkbredis()
    {
        if (!extension_loaded('redis')) {
            EchoLog('This core needs to use php\'s redis extension for high-precision calculations.', 'error', true);
        }
    }

    public function checkphpversion()
    {
        if (version_compare(phpversion(), '7.0', '<')) {
            EchoLog('The core version of PHP that needs to be relied on is 7.0 and above. The current PHP version is:: ' . phpversion(), 'error', true);
        }
    }

    public function checkswooleversion()
    {
        if (!extension_loaded('swoole')) {
            EchoLog('It is detected that there is no Swoole extension currently, please install Swoole extension for PHP', 'error', true);
        }
    }

    public function checkbcmath()
    {
        if (!extension_loaded('bcmath')) {
            EchoLog('This core needs to use php\'s bcmath extension for high-precision calculations.', 'error', true);
        }
    }
}
