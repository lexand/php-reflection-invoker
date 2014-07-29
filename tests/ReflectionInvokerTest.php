<?php

namespace reflection;

/**
 * @author alex
 * @date 05.06.14
 * @time 16:23
 */
class ReflectionInvokerTest extends \PHPUnit_Framework_TestCase
{

    public function testInvokeProtected()
    {
        $a = new A;
        $this->assertEquals('prot', ReflectionInvoker::getSingleInstance()->invoke($a, 'prot'));
    }

    public function testInvokePrivate()
    {
        $a = new A;
        $this->assertEquals('priv', ReflectionInvoker::getSingleInstance()->invoke($a, 'priv'));
    }

    public function testInvokeStaticProtected()
    {
        $this->assertEquals('sprot', ReflectionInvoker::getSingleInstance()->invoke('\reflection\A', 'sprot'));
    }

    public function testInvokeStaticPrivate()
    {
        $this->assertEquals('spriv', ReflectionInvoker::getSingleInstance()->invoke('\reflection\A', 'spriv'));
    }

    public function testInvokeProtectedArgs()
    {
        $a = new A;
        $this->assertEquals(123, ReflectionInvoker::getSingleInstance()->invoke($a, 'prota', [123]));
    }

    public function testInvokePrivateArgs()
    {
        $a = new A;
        $this->assertEquals(123, ReflectionInvoker::getSingleInstance()->invoke($a, 'priva', [123]));
    }

    public function testInvokeStaticProtectedArgs()
    {
        $this->assertEquals(123, ReflectionInvoker::getSingleInstance()->invoke('\reflection\A', 'sprota', [123]));
    }

    public function testInvokeStaticPrivateArgs()
    {
        $this->assertEquals(123, ReflectionInvoker::getSingleInstance()->invoke('\reflection\A', 'spriva', [123]));
    }

    public function testFQMNVirtual()
    {
        $a = new A;
        $this->assertEquals('prot', ReflectionInvoker::getSingleInstance()->invokeFQMN('\reflection\A::prot', $a));
    }

    public function testFQMNVirtualArgs()
    {
        $a = new A;
        $this->assertEquals(123, ReflectionInvoker::getSingleInstance()->invokeFQMN('\reflection\A::prota', $a, [123]));
    }

    public function testFQMNStatic()
    {
        $this->assertEquals('sprot', ReflectionInvoker::getSingleInstance()->invokeFQMN('\reflection\A::sprot'));
    }

    public function testFQMNStaticArgs()
    {
        $this->assertEquals(123,
                            ReflectionInvoker::getSingleInstance()->invokeFQMN('\reflection\A::sprota', null, [123]));
    }


}

class A
{
    protected function prot()
    {
        return 'prot';
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function priv()
    {
        return 'priv';
    }

    protected static function sprot()
    {
        return 'sprot';
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private static function spriv()
    {
        return 'spriv';
    }

    protected function prota($a)
    {
        return $a;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function priva($a)
    {
        return $a;
    }

    protected static function sprota($a)
    {
        return $a;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private static function spriva($a)
    {
        return $a;
    }
}