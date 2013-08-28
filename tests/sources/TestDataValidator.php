<?php

require_once(TESTDIR . 'simpletest/autorun.php');
require_once(SUBSDIR . '/DataValidator.class.php');

/**
 * TestCase class for DataValidator
 */
class TestValidator extends UnitTestCase
{
	/**
	 * Tests for email validation in particular, of DataValidator
	 */
	function testEmail()
	{
		$validator = new Data_Validator();
		$validator->sanitation_rules(array('from_email' => 'trim'));
		$validator->validation_rules(array('from_email' => 'valid_email'));

		$values = array('from_email' => '2');
		$this->assertFalse($validator->validate($values));

		$values = array('from_email' => '2@');
		$this->assertFalse($validator->validate($values));

		$values = array('from_email' => '2@43');
		$this->assertFalse($validator->validate($values));

		$values = array('from_email' => 'e2@ test.com');
		$this->assertFalse($validator->validate($values));

		$values = array('from_email' => ' 2@gmail.net');
		$this->assertTrue($validator->validate($values));

		$values = array('from_email' => 'test@test.com 		');
		$this->assertTrue($validator->validate($values));

		$values = array('from_email' => 'me@gmail.com');
		$this->assertTrue($validator->validate($values));
	}

	/**
	 * Tests for alpha, alphanumeric etc rules.
	 */
	function testAlphas()
	{
		$validator = new Data_Validator();
		$validator->validation_rules(array('some_alpha' => 'required|alpha'));

		$values = array('some_alpha' => '');
		$this->assertFalse($validator->validate($values));

		$this->assertFalse($validator->validate(array('some_alpha' => 'alpha7notonly')));
		$this->assertFalse($validator->validate(array('some_alpha' => 'alpha notonly')));

		$validator->validation_rules(array('some_alpha' => 'required|alpha_numeric'));
		$this->assertTrue($validator->validate(array('some_alpha' => 'alpha7numeric')));
	}

}
