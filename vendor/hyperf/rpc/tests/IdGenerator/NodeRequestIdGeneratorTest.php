<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HyperfTest\Rpc\IdGenerator;

use Hyperf\Rpc\IdGenerator\NodeRequestIdGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class NodeRequestIdGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $generator = new NodeRequestIdGenerator();
        $id = $generator->generate();
        $this->assertRegExp('/^[0-9a-zA-Z]{7,}$/', $id);
    }

    public function testDecode()
    {
        $generator = new NodeRequestIdGenerator();
        $ret = $generator->decode('hiHcSR3RXy2OqQ');
        $this->assertInstanceOf(\DateTime::class, $ret['time']);
        $this->assertEquals('2019-08-02 06:04:56.546000', $ret['time']->format('Y-m-d H:i:s.u'));
        $this->assertEquals('02:42:1d:29:3a:1d', $ret['node']);
    }
}
