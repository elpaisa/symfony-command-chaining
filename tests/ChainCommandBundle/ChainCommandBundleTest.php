<?php
/**
 * Created by IntelliJ IDEA.
 * User: johnleytondiaz
 * Date: 8/31/16
 * Time: 12:04 PM
 */

namespace Tests\ChainCommandBundle;

use ChainCommandBundle\ChainCommandBundle;
use Symfony\Component\DependencyInjection\Exception\LogicException;

class ChainCommandBundleTest extends \PHPUnit_Framework_TestCase
{
    public $chainBundle;

    protected function setUp()
    {
        parent::setUp();

        $this->chainBundle = $this->getMockBuilder(ChainCommandBundle::class)
            ->setMethods(['registerAsMemberOf'])
            ->getMock();
    }

    /**
     * Test registerAsMemberOf method
     *
     * @author johnleytondiaz
     * @expectedException \Exception
     * @covers \ChainCommandBundle::registerAsMemberOf
     * @return void
     */
    public function testRegisterAsMemberOfException()
    {
        $chain = 'foo:chain';
        $member = 'foo:member';
        $chainBundle = new ChainCommandBundle();
        $chainBundle->registerAsMemberOf($chain, $member);
        $chainBundle->registerAsMemberOf($chain, $member);

        $this->assertEquals(1, count($chainBundle->chain[$chain]));

    }


}