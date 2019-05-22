<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	* Name:  Twilio
	*
	* Author: Ben Edmunds
	*		  ben.edmunds@gmail.com
	*         @benedmunds
	*
	* Location:
	*
	* Created:  03.29.2011
	*
	* Description:  Twilio configuration settings.
	*
	*
	*/

	/**
	 * Mode ("sandbox" or "prod")
	 **/
	$config['mode']   = 'sandbox';

	
	//$config['account_sid']   = 'ACf4a6a4c1822501120c7a926839d8c3d3';
	$config['account_sid']   = 'ACf3eb9c495dcca720eb8078ad1d5f327b';

	/**
	 * Auth Token
	 **/
	//$config['auth_token']    = 'feef39eaede451726ac963b1d8c11777';
	$config['auth_token']    = 'b4226671c39e656808fbcd386576c414';

	/**
	 * API Version
	 **/
	$config['api_version']   = '2010-04-01';

	/**
	 * Twilio Phone Number
	 **/
	//$config['number']        = '+14387956844'; //+12018775339   +12017464477 +19165121873  +1 617 917 5884
	$config['number']        = '+34931071610'; //+12018775339   +12017464477 +19165121873  +1 617 917 5884


/* End of file twilio.php */
