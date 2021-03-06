<?php

/**
 * @name      ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0 Alpha
 *
 */

if (!defined('ELK'))
	die('No access...');

/**
 *  This class is an experiment for the job of correctly detecting browsers and settings needed for them.
 * - Detects the following browsers
 * - Opera, Webkit, Firefox, Web_tv, Konqueror, IE, Gecko
 * - Webkit variants: Chrome, iphone, blackberry, android, safari, ipad, ipod
 * - Opera Versions: 6, 7, 8 ... 10 ... and mobile mini and mobi
 * - Firefox Versions: 1, 2, 3 .... 11 ...
 * - Chrome Versions: 1 ... 18 ...
 * - IE Versions: 4, 5, 5.5, 6, 7, 8, 9, 10 ... mobile and Mac
 * - Nokia
 * - Basic mobile and tablet (ipad, android and tablet PC)
 */
class Browser_Detector
{
	/**
	 * Holds all browsers information. Its contents will be placed into $context['browser'].
	 *
	 * @var array
	 */
	private $_browsers = null;

	/**
	 * Holds if the detected device may be a mobile one
	 *
	 * @var type
	 */
	private $_is_mobile = null;

	/**
	 * Holds if the detected device may be a tablet
	 *
	 * @var type
	 */
	private $_is_tablet = null;

	/**
	 * User agent
	 * @var string
	 */
	private $_ua = null;

	/**
	 * The main method of this class, you know the one that does the job: detect the thing.
	 *  - determines the user agent (browser) as best it can.
	 * The method fills the instance variables _is_mobile and _is_tablet,
	 * and the _browsers array. When it returns, the Browser_Detector can
	 * be queried for information on client browser.
	 * It also attempts to detect if the client is a robot.
	 */
	function detectBrowser()
	{
		global $context, $user_info;

		// Init
		$this->_browsers = array();
		$this->_is_mobile = false;
		$this->_is_tablet = false;

		// Initialize some values we'll set differently if necessary...
		$this->_browsers['needs_size_fix'] = false;

		// Saves us many many calls
		$req = request();
		$this->_ua = $req->user_agent();

		// One at a time, one at a time, and in this order too
		if ($this->isOpera())
			$this->_setupOpera();
		// Them webkits need to be set up too
		elseif ($this->isWebkit())
			$this->_setupWebkit();
		// We may have work to do on Firefox...
		elseif ($this->isFirefox())
			$this->_setupFirefox();
		// Old friend, old frenemy
		elseif ($this->isIe())
			$this->_setupIe();

		// Just a few mobile checks
		$this->isOperaMini();
		$this->isOperaMobi();

		// Be you robot or human?
		if ($user_info['possibly_robot'])
		{
			// This isn't meant to be reliable, it's just meant to catch most bots to prevent PHPSESSID from showing up.
			$this->_browsers['possibly_robot'] = !empty($user_info['possibly_robot']);

			// Robots shouldn't be logging in or registering.  So, they aren't a bot.  Better to be wrong than sorry (or people won't be able to log in!), anyway.
			if ((isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('login', 'login2', 'register'))) || !$user_info['is_guest'])
				$this->_browsers['possibly_robot'] = false;
		}
		else
			$this->_browsers['possibly_robot'] = false;

		// Fill out the historical array as needed to support old mods that don't use isBrowser
		$this->_fillInformation();

		// Last step ...
		$this->_setupBrowserPriority();

		// Now see what you've done!
		$context['browser'] = $this->_browsers;
	}

	/**
	* Determine if the browser is Opera or not
	*
	* @return boolean true if the browser is Opera otherwise false
	*/
	function isOpera()
	{
		if (!isset($this->_browsers['is_opera']))
			$this->_browsers['is_opera'] = strpos($this->_ua, 'Opera') !== false;
		return $this->_browsers['is_opera'];
	}

	/**
	* Determine if the browser is IE or not
	*
	* @return boolean true if the browser is IE otherwise false
	*/
	function isIe()
	{
		// I'm IE, Yes I'm the real IE; All you other IEs are just imitating.
		if (!isset($this->_browsers['is_ie']))
			$this->_browsers['is_ie'] = !$this->isOpera() && !$this->isGecko() && !$this->isWebTv() && preg_match('~MSIE \d+~', $this->_ua) === 1;
		return $this->_browsers['is_ie'];
	}

	/**
	* Determine if the browser is a Webkit based one or not
	*
	* @return boolean true if the browser is Webkit based otherwise false
	*/
	function isWebkit()
	{
		if (!isset($this->_browsers['is_webkit']))
			$this->_browsers['is_webkit'] = strpos($this->_ua, 'AppleWebKit') !== false;
		return $this->_browsers['is_webkit'];
	}

	/**
	* Determine if the browser is Firefox or one of its variants
	*
	* @return boolean true if the browser is Firefox otherwise false
	*/
	function isFirefox()
	{
		if (!isset($this->_browsers['is_firefox']))
			$this->_browsers['is_firefox'] = preg_match('~(?:Firefox|Ice[wW]easel|IceCat|Shiretoko|Minefield)/~', $this->_ua) === 1 && $this->isGecko();
		return $this->_browsers['is_firefox'];
	}

	/**
	* Determine if the browser is WebTv or not
	*
	* @return boolean true if the browser is WebTv otherwise false
	*/
	function isWebTv()
	{
		if (!isset($this->_browsers['is_web_tv']))
			$this->_browsers['is_web_tv'] = strpos($this->_ua, 'WebTV') !== false;
		return $this->_browsers['is_web_tv'];
	}

	/**
	* Determine if the browser is konqueror or not
	*
	* @return boolean true if the browser is konqueror otherwise false
	*/
	function isKonqueror()
	{
		if (!isset($this->_browsers['is_konqueror']))
			$this->_browsers['is_konqueror'] = strpos($this->_ua, 'Konqueror') !== false;
		return $this->_browsers['is_konqueror'];
	}

	/**
	* Determine if the browser is Gecko or not
	*
	* @return boolean true if the browser is Gecko otherwise false
	*/
	function isGecko()
	{
		if (!isset($this->_browsers['is_gecko']))
			$this->_browsers['is_gecko'] = strpos($this->_ua, 'Gecko') !== false && !$this->isWebkit() && !$this->isKonqueror();
		return $this->_browsers['is_gecko'];
	}

	/**
	* Determine if the browser is OperaMini or not
	*
	* @return boolean true if the browser is OperaMini otherwise false
	*/
	function isOperaMini()
	{
		if (!isset($this->_browsers['is_opera_mini']))
			$this->_browsers['is_opera_mini'] = (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) || stripos($this->_ua, 'opera mini') !== false);
		if ($this->_browsers['is_opera_mini'])
			$this->_is_mobile = true;
		return $this->_browsers['is_opera_mini'];
	}

	/**
	* Determine if the browser is OperaMobi or not
	*
	* @return boolean true if the browser is OperaMobi otherwise false
	*/
	function isOperaMobi()
	{
		if (!isset($this->_browsers['is_opera_mobi']))
			$this->_browsers['is_opera_mobi'] = stripos($this->_ua, 'opera mobi') !== false;
		if ($this->_browsers['is_opera_mobi'])
			$this->_is_mobile = true;
		return $this->_browsers['is_opera_mini'];
	}

	/**
	 * Detect Safari / Chrome / iP[ao]d / iPhone / Android / Blackberry from webkit.
	 *  - set the browser version for Safari and Chrome
	 *  - set the mobile flag for mobile based useragents
	 */
	private function _setupWebkit()
	{
		$this->_browsers += array(
			'is_chrome' => strpos($this->_ua, 'Chrome') !== false,
			'is_iphone' => (strpos($this->_ua, 'iPhone') !== false || strpos($this->_ua, 'iPod') !== false) && strpos($this->_ua, 'iPad') === false,
			'is_blackberry' => stripos($this->_ua, 'BlackBerry') !== false || strpos($this->_ua, 'PlayBook') !== false,
			'is_android' => strpos($this->_ua, 'Android') !== false,
			'is_nokia' => strpos($this->_ua, 'SymbianOS') !== false,
			'is_ipad' => strpos($this->_ua, 'iPad') !== false,
		);

		// blackberry, playbook, iphone, nokia, android and ipods set a mobile flag
		if ($this->_browsers['is_iphone'] || $this->_browsers['is_blackberry'] || $this->_browsers['is_android'] || $this->_browsers['is_nokia'])
			$this->_is_mobile = true;

		// iPad and droid tablets get a tablet flag
		$this->_browsers['is_android_tablet'] = $this->_browsers['is_android'] && strpos($this->_ua, 'Mobile') === false;
		if ($this->_browsers['is_ipad'] || $this->_browsers['is_android_tablet'])
			$this->_is_tablet = true;

		$this->_browsers['is_safari'] = strpos($this->_ua, 'Safari') !== false && !$this->_browsers['is_chrome'] && !$this->_browsers['is_iphone'];

		// if Chrome, get the major version
		if ($this->_browsers['is_chrome'])
		{
			if (preg_match('~chrome[/]([0-9][0-9]?[.])~i', $this->_ua, $match) === 1)
				$this->_browsers['is_chrome' . (int) $match[1]] = true;
		}

		// or if Safari get its major version
		if ($this->_browsers['is_safari'])
		{
			if (preg_match('~version/?(.*)safari.*~i', $this->_ua, $match) === 1)
				$this->_browsers['is_safari' . (int) trim($match[1])] = true;
		}
	}

	/**
	 * Additional IE checks and settings.
	 *  - determines the version of the IE browser in use
	 *  - detects ie4 onward
	 *  - attempts to distinguish between IE and IE in compatabilty view
	 *  - checks for old IE on macs as well, since we can
	 */
	private function _setupIe()
	{
		$this->_browsers['is_ie_compat_view'] = false;

		// get the version of the browser from the msie tag
		if (preg_match('~MSIE\s?([0-9][0-9]?.[0-9])~i', $this->_ua, $msie_match) === 1)
		{
			$msie_match[1] = trim($msie_match[1]);
			$msie_match[1] = (($msie_match[1] - (int) $msie_match[1]) == 0) ? (int) $msie_match[1] : $msie_match[1];
			$this->_browsers['is_ie' . $msie_match[1]] = true;
		}

		// "modern" ie uses trident 4=ie8, 5=ie9, 6=ie10, even in compatability view
		if (preg_match('~Trident/([0-9.])~i', $this->_ua, $trident_match) === 1)
		{
			$this->_browsers['is_ie' . ((int) $trident_match[1] + 4)] = true;

			// If trident is set, see the (if any) msie tag in the user agent matches ... if not its in some compatablity view
			if (isset($msie_match[1]) && ($msie_match[1] < $trident_match[1] + 4))
				$this->_browsers['is_ie_compat_view'] = true;
		}

		// Detect true IE6 and IE7 and not IE in compat mode.
		$this->_browsers['is_ie7'] = !empty($this->_browsers['is_ie7']) && ($this->_browsers['is_ie_compat_view'] === false);
		$this->_browsers['is_ie6'] = !empty($this->_browsers['is_ie6']) && ($this->_browsers['is_ie_compat_view'] === false);

		// IE mobile 7 or 9, ... shucks why not
		if ((!empty($this->_browsers['is_ie7']) && strpos($this->_ua, 'IEMobile/7') !== false) || (!empty($this->_browsers['is_ie9']) && strpos($this->_ua, 'IEMobile/9') !== false))
		{
			$this->_browsers['is_ie_mobi'] = true;
			$this->_is_mobile = true;
		}

		// Tablets as well, someone may win one
		if (strpos($this->_ua, 'Tablet PC') !== false)
		{
			$this->_browsers['is_tablet_pc'] = true;
			$this->_is_tablet = true;
		}

		// And some throwbacks to a bygone era, deposited here like cholesterol in your arteries
		$this->_browsers += array(
			'is_ie4' => !empty($this->_browsers['is_ie4']) && !$this->_browsers['is_web_tv'],
			'is_mac_ie' => strpos($this->_ua, 'MSIE 5.') !== false && strpos($this->_ua, 'Mac') !== false
		);

		// Before IE8 we need to fix IE... lots!
		$this->_browsers['ie_standards_fix'] = (($this->_browsers['is_ie6'] === true) || ($this->_browsers['is_ie7'] === true)) ? true : false;

		// We may even need a size fix...
		$this->_browsers['needs_size_fix'] = (!empty($this->_browsers['is_ie5']) || !empty($this->_browsers['is_ie5.5']) || !empty($this->_browsers['is_ie4'])) && !$this->_browsers['is_mac_ie'];
	}

	/**
	 * Additional firefox checks.
	 * - Gets the version of the FF browser in use
	 * - Considers all FF variants as FF including IceWeasel, IceCat, Shiretoko and Minefiled
	 */
	private function _setupFirefox()
	{
		if (preg_match('~(?:Firefox|Ice[wW]easel|IceCat|Shiretoko|Minefield)[\/ \(]([^ ;\)]+)~', $this->_ua, $match) === 1)
			$this->_browsers['is_firefox' . (int) $match[1]] = true;
	}

	/**
	 * More Opera checks if we are opera.
	 *  - checks for the version of Opera in use
	 *  - uses checks for 10 first and falls through to <9
	 */
	private function _setupOpera()
	{
		// Opera 10+ uses the version tag at the end of the string
		if (preg_match('~\sVersion/([0-9]+)\.[0-9]+(?:\s*|$)~', $this->_ua, $match))
			$this->_browsers['is_opera' . (int) $match[1]] = true;
		// Opera pre 10 is supposed to uses the Opera tag alone, as do some spoofers
		elseif (preg_match('~Opera[ /]([0-9]+)(?!\\.[89])~', $this->_ua, $match))
			$this->_browsers['is_opera' . (int) $match[1]] = true;

		// Needs size fix?
		$this->_browsers['needs_size_fix'] = !empty($this->_browsers['is_opera6']);
	}

	/**
	 * Get the browser name that we will use in the <body id="this_browser">
	 *  - The order of each browser in $browser_priority is important
	 *  - if you want to have id='ie6' and not id='ie' then it must appear first in the list of ie browsers
	 *  - only sets browsers that may need some help via css for compatablity
	 */
	private function _setupBrowserPriority()
	{
		global $context;

		if ($this->_is_mobile && !$this->_is_tablet)
			$context['browser_body_id'] = 'mobile';
		elseif ($this->_is_tablet)
			$context['browser_body_id'] = 'tablet';
		else
		{
			// add in any specific detection conversions here if you want a special body id e.g. 'is_opera9' => 'opera9'
			$browser_priority = array(
				'is_ie8' => 'ie8',
				'is_ie' => 'ie',
				'is_firefox' => 'firefox',
				'is_chrome' => 'chrome',
				'is_safari' => 'safari',
				'is_opera' => 'opera',
				'is_konqueror' => 'konqueror',
			);

			$context['browser_body_id'] = 'elkarte';
			$active = array_reverse(array_keys($this->_browsers, true));
			foreach ($active as $key => $browser)
			{
				if (array_key_exists($browser, $browser_priority))
				{
					$context['browser_body_id'] = $browser_priority[$browser];
					break;
				}
			}
		}
	}

	/**
	 * Fill out the historical array
	 *  - needed to support old mods that don't use isBrowser
	 */
	private function _fillInformation()
	{
		$this->_browsers += array(
			'is_opera' => false,
			'is_opera6' => false,
			'is_opera7' => false,
			'is_opera8' => false,
			'is_opera9' => false,
			'is_opera10' => false,
			'is_webkit' => false,
			'is_mac_ie' => false,
			'is_web_tv' => false,
			'is_konqueror' => false,
			'is_firefox' => false,
			'is_firefox1' => false,
			'is_firefox2' => false,
			'is_firefox3' => false,
			'is_iphone' => false,
			'is_android' => false,
			'is_chrome' => false,
			'is_safari' => false,
			'is_gecko'  => false,
			'is_ie8' => false,
			'is_ie7' => false,
			'is_ie6' => false,
			'is_ie5.5' => false,
			'is_ie5' => false,
			'is_ie' => false,
			'is_ie4' => false,
			'ie_standards_fix' => false,
			'needs_size_fix' => false,
			'possibly_robot' => false,
		);
	}
}