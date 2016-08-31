<?php
/**
 * Created by IntelliJ IDEA.
 * User: johnleytondiaz
 * Date: 8/31/16
 * Time: 12:04 PM
 */

namespace Tests\ChainCommandBundle;

use ChainCommandBundle\ChainCommandBundle;

class ChainCommandBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ChainCommandBundle
     */
    public $chainBundle;

    /**
     * @coversNothing
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->chainBundle = new ChainCommandBundle();

        $container = $this->getMockBuilder(\Symfony\Component\DependencyInjection\Container::class)
                          ->setMethods(['get'])
                          ->getMock();
        $container->expects($this->any())
                  ->method('get')
                  ->willReturn(new \Symfony\Bridge\Monolog\Logger('app'));

        $this->chainBundle->setContainer($container);
    }

    /**
     * Test registerAsMemberOf method
     *
     * @author johnleytondiaz
     * @covers \ChainCommandBundle::registerAsMemberOf
     * @return void
     */
    public function testRegisterAsMemberOf()
    {
        $chain  = 'foo:chain';
        $member = 'foo:member';
        $this->chainBundle->registerAsMemberOf($chain, $member);

        $this->assertTrue(count($this->chainBundle->chain) > 0);
        $this->assertEquals(array_keys($this->chainBundle->chain)[0], $chain);

        $this->chainBundle->chain = [];
    }

    /**
     * Test registerAsMemberOf method exception
     *
     * @author johnleytondiaz
     * @expectedException \Exception
     * @covers \ChainCommandBundle::registerAsMemberOf
     * @return void
     */
    public function testRegisterAsMemberOfException()
    {
        $chain  = 'foo:chain';
        $member = 'foo:member';
        $this->chainBundle->registerAsMemberOf($chain, $member);
        $this->chainBundle->registerAsMemberOf($chain, $member);

        $this->assertEquals(1, count($this->chainBundle->chain[$chain]));

        $this->chainBundle->chain = [];
    }

    /**
     * Test registerDependencies method
     *
     * @author johnleytondiaz
     * @covers \ChainCommandBundle::registerDependencies
     * @return void
     */
    public function testRegisterDependencies()
    {

    }
}