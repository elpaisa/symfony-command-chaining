<?php
/**
 * Created by IntelliJ IDEA.
 * User: johnleytondiaz
 * Date: 8/31/16
 * Time: 12:04 PM
 *
 * Test for Main class to accomplish command chaining
 *
 * @package  symfony-command-chaining
 * @author   johnleytondiaz <jdiaz@secureaudit.co>
 */
namespace Tests\ChainCommandBundle;

use ChainCommandBundle\ChainCommandBundle;
use FooBundle\FooBundle;
use Symfony\Component\Console\Output\ConsoleOutput;

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
     * Cleans chain attribute and returns the mocked class
     *
     * @return ChainCommandBundle
     */
    public function getChainingBundle()
    {
        $this->chainBundle->chain = [];

        return $this->chainBundle;
    }

    /**
     * @param string $chain
     * @param string $member
     * @return FooBundle
     */
    public function getCommandMock($chain, $member)
    {
        $command = $this
            ->getMockBuilder(\FooBundle\FooBundle::class)
            ->setMethods(['depends', 'getCommandName'])
            ->getMock();
        $command
            ->expects($this->any())
            ->method('depends')
            ->willReturn([$chain]);

        $command
            ->method('getCommandName')
            ->willReturn($member);

        return $command;
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
        $chain       = 'foo:chain';
        $member      = 'foo:member';
        $chainBundle = $this->getChainingBundle();
        $chainBundle->registerAsMemberOf($chain, $member);

        $this->assertTrue(count($chainBundle->chain) > 0);
        $this->assertEquals(array_keys($chainBundle->chain)[0], $chain);

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
        $chain       = 'foo:chain';
        $member      = 'foo:member';
        $chainBundle = $this->getChainingBundle();
        $chainBundle->registerAsMemberOf($chain, $member);
        $chainBundle->registerAsMemberOf($chain, $member);

        $this->assertEquals(1, count($chainBundle->chain[$chain]));

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
        $chain   = 'foo:chain';
        $member  = 'foo:member';
        $command = $this->getCommandMock($chain, $member);

        $chainBundle = $this->getChainingBundle();
        $chainBundle->registerDependencies($command);

        $this->assertEquals(array_keys($chainBundle->chain)[0], $chain);
        $this->assertEquals($chainBundle->chain[$chain][0], $member);
    }

    /**
     * Test isChild method
     *
     * @author johnleytondiaz
     * @covers \ChainCommandBundle::isChild
     * @return void
     */
    public function testIsChild()
    {
        $chain       = 'foo:chain';
        $member      = 'foo:member';
        $chainBundle = $this->getChainingBundle();
        $chainBundle->registerAsMemberOf($chain, $member);

        $isChild = $chainBundle->isChild($member);

        $this->assertInternalType('array', $isChild);

        $this->chainBundle->chain = [];

        $isChild = $chainBundle->isChild($member);

        $this->assertTrue($isChild === false);

    }

    /**
     * Test execute method
     *
     * @author johnleytondiaz
     * @covers \ChainCommandBundle::execute
     * @return void
     */
    public function testExecute()
    {
        $chain       = 'foo:chain';
        $member      = 'foo:member';
        $command     = $this->getCommandMock($chain, $member);
        $chainBundle = $this->getChainingBundle();

        $chainBundle->registerDependencies($command);

        $execute = $chainBundle->execute($command->getName(), new ConsoleOutput(), function () {
            return 1;
        });

        $this->assertTrue($execute === 1);

    }
}