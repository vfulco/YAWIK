<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Form;

use Core\Form\SummaryFormInterface;
use Organizations\Form\Employees;

/**
 * Test for Employees Form.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Form
 */
class EmployeesTest extends \PHPUnit_Framework_TestCase
{

    public function testExtendsBaseClassAndHasExpectedPropertyValues()
    {
        $target = new Employees();

        $this->assertInstanceOf('\Core\Form\SummaryForm', $target);
        $this->assertAttributeEquals('Organizations/EmployeesFieldset', 'baseFieldset', $target);
        $this->assertAttributeEquals(SummaryFormInterface::DISPLAY_SUMMARY, 'displayMode', $target);

    }
}