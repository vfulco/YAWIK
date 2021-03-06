<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Factory\Controller\Plugin;

use Auth\AuthenticationService;
use Organizations\Factory\Controller\Plugin\AcceptInvitationHandlerFactory;

/**
 * Tests for \Organizations\Factory\Controller\Plugin\AcceptInvitationHandlerFactory
 * 
 * @covers \Organizations\Factory\Controller\Plugin\AcceptInvitationHandlerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Factory
 * @group Organizations.Factory.Controller
 * @group Organizations.Factory.Controller.Plugin
 */
class AcceptInvitationHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsInterface()
    {
        $this->assertInstanceOf('\Zend\ServiceManager\FactoryInterface', new AcceptInvitationHandlerFactory());
    }

    /**
     * @testdox Creates a proper configured AcceptInvitationHandler plugin instance.
     */
    public function testCreateService()
    {
        $userRep = $this->getMockBuilder('\Auth\Repository\User')->disableOriginalConstructor()->getMock();
        $orgRep = $this->getMockBuilder('\Organizations\Repository\Organization')->disableOriginalConstructor()->getMock();

        $repositories = $this->getMockBuilder('\Core\Repository\RepositoryService')->disableOriginalConstructor()->getMock();
        $repositories->expects($this->exactly(2))->method('get')->will($this->returnValueMap(array(
            array('Auth/User', $userRep),
            array('Organizations', $orgRep)
        )));

        $auth = $this->getMockBuilder('\Auth\AuthenticationService')->disableOriginalConstructor()->getMock();

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
        $services->expects($this->exactly(2))->method('get')->will($this->returnValueMap(
            array(
                array('repositories', true, $repositories),
                array('AuthenticationService', true, $auth)
            )
        ));

        $plugins = $this->getMockBuilder('\Zend\Mvc\Controller\PluginManager')->disableOriginalConstructor()->getMock();
        $plugins->expects($this->once())->method('getServiceLocator')->willReturn($services);

        $target = new AcceptInvitationHandlerFactory();
        /*
         * Test start here
         */

        $plugin = $target->createService($plugins);

        $this->assertInstanceOf('\Organizations\Controller\Plugin\AcceptInvitationHandler', $plugin);
        $this->assertSame($userRep, $plugin->getUserRepository());
        $this->assertSame($orgRep, $plugin->getOrganizationRepository());
        $this->assertSame($auth, $plugin->getAuthenticationService());
    }

}