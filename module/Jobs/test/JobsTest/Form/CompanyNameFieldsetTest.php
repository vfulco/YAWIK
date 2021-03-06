<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Form;

use Jobs\Form\CompanyNameFieldset;

/**
 * Test for CompanyNameFieldset
 *
 * @covers \Jobs\Form\CompanyNameFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Form
 */
class CompanyNameFieldsetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Class under Test
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|CompanyNameFieldset
     */
    private $target;

    public function setUp()
    {
        $this->target = $this->getMockBuilder('\Jobs\Form\CompanyNameFieldset')
                             ->disableOriginalConstructor()
                             ->setMethods(array('setAttribute', 'setName', 'add'))
                             ->getMock();

    }

    /**
     * @testdox Extends \Zend\Form\Fieldset
     */
    public function testExtendsFieldset()
    {
        $this->assertInstanceOf('\Zend\Form\Fieldset', $this->target);
    }

    /**
     * @testdox Configures itself in the init() method.
     */
    public function testInitialization()
    {
        $this->target->expects($this->once())
                     ->method('setAttribute')
                     ->with('id', 'jobcompanyname-fieldset');

        $this->target->expects($this->once())
                     ->method('setName')
                     ->with('jobCompanyName');

        $addParam = array(
            'type' => 'Jobs/HiringOrganizationSelect',
            'property' => true,
            'name' => 'companyId',
            'options' => array(
                'label' =>  'Companyname',
            ),
            'attributes' => array(
                'data-placeholder' => 'Select hiring organization',
            ),
        );

        $this->target->expects($this->once())
                     ->method('add')
                     ->with($addParam);

        $this->target->init();
    }
}