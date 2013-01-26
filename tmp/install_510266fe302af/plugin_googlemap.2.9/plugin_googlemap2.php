<?php
/**
* @version $Id $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
 */
/**
 * plugin_googlemap2.php,v 2.9 2007/09/22 18:31:01
 * @copyright (C) Reumer.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
 /* ----------------------------------------------------------------
 * 2007-09-22 version 2.9: Improved by Mike Reumer
 * - #6022: strip <br> etc out of address for geocoding.
 * - #6023: Center and zoom the map based on the kml-file
 *   - If coordinates are entered center and zoom the map based on these coordinates
 *   - If no coordinates are entered center and zoom the map based on the kml-file
 *   - moved KML-file actions from end to middle of code.
 * - #6024: Show direction form when no text
 *   - If dir=1 make always a infowindow with the directions form
 * - #6025: Polylines don't show in Opera.
 *   - Added lines: var _mSvgForced = true; var _mSvgEnabled = true; when browser is Opera!
 * - #6037: Labels of directions form couln't be changed.
 *   - Names of parameters didn't match.
 * - #6131: Solved problems with PHP5 and opening files and wrong coding of xml.
 * - #6470: Added debug mechanisme for debugging options.
 * - #6471: If server side geocoding fails do it at the client side.
 * - #6472: Only remove quotes that surround the content of a parameter and change double qoutes to single quotes in text
 *   So HTML is better resolved for generated bu EasyTube plugin and others.
 * - #6637: The direction form doesn't have a css-class.
 *   - Gave the direction form a class for css-styling.
 *   - Place the direction form within the first pair of div before the closing div
 * - #7055: The replacement of the mosmap code isn't done correctly step by step.
 *   - replace str_replace with preg_replace for 1 item.
 * - #7132: Placed kml-file also in overviewmap.
 *   - Created a second xml variable for the kml-file for the overview map.
 /* ----------------------------------------------------------------
 * 2007-03-24 version 2.8: Improved by Mike Reumer and Arjan Menger of WELLdotCOM (www.welldotcom.nl) with donation
 * - artf6173: wheel-mouse zooming
 *   - Problem with multiple maps solved by naming function unique.
 *   - Problem with scroll wheel moves page too by adding cancelevent
 * - artf7734: Load kml overlay out of file
 *   - New parameter for KML-overlay
 * - #4494: Add buttons for driving directions
 * - #5274 PHP problem URL file-access is disabled in the server configuration
 * ---------------------------------------------------------------- */
/* ----------------------------------------------------------------
 * 2007-02-10 version 2.7: Improved by Keith Slater and Mike Reumer
 * - artf7666: Check if javascript is enabled or browser is compatible.
 * - artf7564: Multiple urls
 *   - Added the option to get the single key or search in the multiple url's for a key.
 * - artf6182: Localization
 *   - Get language from the site itself
 *	 - Get language as parameter from the {mosmap}
 *   - Set language if available as parameter or setting
 * ---------------------------------------------------------------- */
/* ----------------------------------------------------------------
 * 2006-12-11 version 2.6: Improved by Eugene Trotsan and Mike Reumer
 * - artf7020: Extra parameter for address.
 *	 - Get the coordinates of the address at google when parameter lon/lat are empty.
 *   - Problem with SimpleXMLElement PHP >= 5
 * - artf6293: Tool tips
 *   - New parameter for tooltip 
 * - artf6995: Turn off overview
 *   - A new value for overview. 2 for overview window to be closed initially.
 * - artf6294 : Turn off infowindow of marker
 *   - New parameter to set infowindow initially closed (0) or open (1 default)
 * - artf6996: Alignment of the map
 *   - New parameter align for the map.
 * ---------------------------------------------------------------- */
/* ----------------------------------------------------------------
 * 2006-10-27 version 2.5: Improved by Mike Reumer
 * - artf6794: Multiple contentitems with maps won't work
 *   - Placed a random text in the name of the googlemap and it's functions.
 * - artf6758: Warning: Wrong value for parameter 4 in call to preg_match_all()
 *   - PREG_OFFSET_CAPTURE has to be combined with PREG_PATTERN_ORDER
 * - artf6755 Call-time pass-by-reference has been deprecated
 *   - Removed & in the call of functions
 * - artf6756 : Warning about variable not defined
 *   - Correctly defined a global parameter
 * ---------------------------------------------------------------- */
/* ----------------------------------------------------------------
 * 2006-10-13 version 2.4: Improved by Mike Reumer
 * - artf6402: Googlemap plugin with tabs not working
 *   - Added a function to look if the offsetposition is changed
 *   - Only make a map when its visible on the page
 *   - Changed event for displaying map in interval for checking if map is visible
 *   - Made important variable in scripts dedicated to the number of the map
 * - artf6456 : Placing defaults of parameters in backoffice
 *   - Created the possibility to set parameters for the plugin in the 
 *     administator of Joomla.
 * - artf6409: Joomla 1.5 support
 *   - Plugin made ready for Joomla 1.5 with configparameter legacy on!
 *   - Calls for Joomla 1.0.x and for Joomla 1.5 created with correct params
 *   - Use a plugin parameter for Joomla 1.5 if plugin is published or not
 * ---------------------------------------------------------------- */
/* ----------------------------------------------------------------
 * 2006-10-02 version 2.3: Improved by Mike Reumer
 * - artf6183: Links not working in Marker
 *   - changed chopping of key and value and translate special htmlcodes
 * - artf6249: Overview initial not the same maptype as map
 *   - changed order of creating controls and setting maptype
 * - artf6280: In IE a big wrong shadow for Marker
 *   - API initialization with wrong version. Removed ".x"
 * ---------------------------------------------------------------- */
/* ----------------------------------------------------------------
 * 2006-09-27 version 2.2: Improved by Mike Reumer
 * - artf6122 Parameters width and height flexible
 *   - Removed px behind width and height
 *   - Changed defaults for width and height parameters
 *   - Check for backward compatibility if units are given
 * - artf6148 Option to turn off the map type selector visibility
 *   - If zoomType is None then no Zoomcontrols (default empty => Small zoomcontrols).
 *   - If showMaptype is 0 then no Maptype controls
 * - artf6176 : Remove mosmap tag if unpublished
 *   - Moved Published within the plugin to remove all the tags
 * - artf6174 : Multiple maps on article w/ {mospagebreak}'s
 *   - Replaced Google maps initialization to the header
 * - Moved default so they are set each {mosmap} 
 * - Settimeout higher for activating to show googlemap (for IE compatibility)
 * - New parameter zoom_new for continues zoom and Doubleclick center and zoom (default 0 => off)
 * - New parameter overview for a overview window at bottom right (default 0 => off)
 * - Scripts made XHTML compliant
 * - artf6150 Documentation with installation
 * ---------------------------------------------------------------- */
/* ----------------------------------------------------------------
 * 2006-09-21: Improved by PILLWAX Industrial Solutions Consulting
 *	- Fixed Script invocation from <body onLoad> to correct JavaScript call
 *   - Add Defaults for parameters
 * ---------------------------------------------------------------- */

/** ensure this file is being included by a parent file */

global $mainframe;
$debug_plugin = '0';
$debug_text = '';

if(method_exists($mainframe,'registerEvent')){
	defined( '_JEXEC' ) or die( 'Restricted access' );
	$mainframe->registerEvent( 'onPrepareContent', 'Pre15x_PluginGoogleMap2' );
}else{
	defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
	$_MAMBOTS->registerFunction( 'onPrepareContent', 'Pre10x_PluginGoogleMap2' );
}

/* If PHP < 5 then htmlspecialchars_decode doesn't exists
 */

if ( !function_exists('htmlspecialchars_decode') )
{
	function htmlspecialchars_decode(&$text, $options="")
	{
		return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $options)));
	}
}

if ( !function_exists('debug_log') ) {

	function debug_log($text)
	{
		global $debug_plugin, $debug_text;
		
		if ($debug_plugin=='1')
			$debug_text .= "\n// ".$text;
	
		return;
	}
}

/* If PHP < 5 then SimpleXMLElement doesn't exists
 */
function get_geo($address, $key)
{
	debug_log("get_geo(".$address.")");

	$coord = '';
	$getpage='';
	$replace = array("\n", "\r", "&lt;br/&gt;", "&lt;br /&gt;", "&lt;br&gt;", "<br>", "<br />", "<br/>");
	$address = str_replace($replace, '', $address);

	debug_log("Address: ".$address);
	
	$uri = "http://maps.google.com/maps/geo?q=".urlencode($address)."&output=xml&key=".$key;
	debug_log("get_geo(".$uri.")");
	
	if ( !class_exists('SimpleXMLElement') )
	{
		// PHP4
		debug_log("SimpleXMLElement doesn't exists so probably PHP 4.x");
		if (!($getpage = file_get_contents($uri))) {
			debug_log("URI couldn't be opened probably ALLOW_URL_FOPEN off");
			if (function_exists('curl_init')) {
				debug_log("curl_init does exists");
				$ch = curl_init();
				$timeout = 5; // set to zero for no timeout
				curl_setopt ($ch, CURLOPT_URL, $uri);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$getpage = curl_exec($ch);
				curl_close($ch);
			} else
				debug_log("curl_init doesn't exists");
		}

		debug_log("Returned page: ".$getpage);

		if (function_exists('mb_detect_encoding')) {
			$enc = mb_detect_encoding($getpage);
			$getpage = mb_convert_encoding($getpage, 'UTF-8', $enc);
		}
			
		if (function_exists('domxml_open_mem')&&($getpage<>'')) {
			$responsedoc = domxml_open_mem($getpage);
			$response = $responsedoc->get_elements_by_tagname("Response");
			if ($response!=null) {
				$placemark = $response[0]->get_elements_by_tagname("Placemark");
				if ($placemark!=null) {
					$point = $placemark[0]->get_elements_by_tagname("Point");
					if ($point!=null) {
						$coords = $point[0]->get_content();
						debug_log("Coordinates: ".join(", ", explode(",", $coords)));
						return $coords;
					}
				}
			}
		}
		debug_log("Coordinates: null");
		return null;
	}
	else
	{
		// PHP5
		debug_log("SimpleXMLElement does exists so probably PHP 5.x");
		if (file_exists($uri)) {
			$getpage = file_get_contents($uri);
		} else {
			debug_log("URI couldn't be opened probably ALLOW_URL_FOPEN off");
			if (function_exists('curl_init')) {
				debug_log("curl_init does exists");
				$ch = curl_init();
				$timeout = 5; // set to zero for no timeout
				curl_setopt ($ch, CURLOPT_URL, $uri);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$getpage = curl_exec($ch);
				curl_close($ch);
			} else
				debug_log("curl_init doesn't exists");
		}

		debug_log("Returned page: ".$getpage);
		if (function_exists('mb_detect_encoding')) {
			$enc = mb_detect_encoding($getpage);
			$getpage = mb_convert_encoding($getpage, 'UTF-8', $enc);
		}

		if ($getpage <>'') {
			$xml = new SimpleXMLElement($getpage);
			$coords = $xml->Response->Placemark->Point->coordinates;
			debug_log("Coordinates: ".join(", ", explode(",", $coords)));
			return $coords;
		}
		debug_log("Coordinates: null");
		return null;
	}
	debug_log("get_geo totally wrong end!");
}
if ( !function_exists('randomkeys') ) {

	function randomkeys($length)
	{
		$key = "";
		$pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
		for($i=0;$i<$length;$i++)
		{
			$key .= $pattern{rand(0,35)};
		}
		return $key;
	}
}

/* Switch call to function of 1.5 to the real module 
 */
function Pre15x_PluginGoogleMap2( &$row, &$params, $page=0 ) {
	global $Itemid, $database;

	// Get Plugin info
	$plugin =& JPluginHelper::getPlugin('content', 'plugin_googlemap2'); 

	$plugin_params = new JParameter( $plugin->params );

	$published = $plugin->published;
	// Solve bug in Joomal 1.5 that when plugin is unpublished that the tag is not removed
	// So use a parameter of plugin to set published for Joomla 1.5
	$published = $plugin_params->get( 'publ', '0' );
	$id = JRequest::getVar('id', null);	

	if( !PluginGoogleMap2($published, $row, $params, $page, $plugin_params, $id) ){
		echo "problem";
	}
	return true;
}

/* Switch call to function of 1.0.x to the real module
 */
function Pre10x_PluginGoogleMap2( $published, &$row, $mask=0, $page=0 ) {
	global $database;

	// load plugin parameters
	$query = "SELECT id"
		. "\n FROM #__mambots"
		. "\n WHERE element = 'plugin_googlemap2'"
		. "\n AND folder = 'content'"
		;
	$database->setQuery( $query );
	$id = $database->loadResult();
	$plugin = new mosMambot( $database );
	$plugin->load( $id );
	$plugin_params =& new mosParameters( $plugin->params );

	$id = intval( mosGetParam( $_REQUEST, 'id', null ) );

	if( !PluginGoogleMap2($published, $row, $mask, $page, $plugin_params, $id) ){
		echo "problem";
	}
	return true;
}

/** Real module
 */
function PluginGoogleMap2( $published, &$row, $mask=0, $page=0, &$params, $id ) {

	global $mosConfig_absolute_path, $mosConfig_live_site,$mainframe, $mosConfig_locale, $debug_plugin, $debug_text;

	$singleregex='/({mosmap\s*)(.*?)(})/si';
	$regex='/{mosmap\s*.*?}/si';

	$cnt=preg_match_all($regex,$row->text,$matches,PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);
	$first=true;

	for($counter = 0; $counter < $cnt; $counter ++)
	{
		// Parameters can get the default from the plugin if not empty or from the administrator part of the plugin

		$debug_plugin = $params->get( 'debug', '0' );
		$width = $params->get( 'width', '100%' );
		$height = $params->get( 'height', '400px' );
		$latitude = $params->get( 'lat', '52.075581' );
		$longitude = $params->get( 'lon', '4.541513' );
		$zoom = $params->get( 'zoom', '10' );
		$showmaptype = $params->get( 'showMaptype', '1' );
		$zoom_new = $params->get( 'zoomNew', '0' );
		$zoom_wheel = $params->get( 'zoomWheel', '0' );
		$overview = $params->get( 'overview', '0' );
		$marker = $params->get( 'marker', '1' );
		$gotoaddr = $params->get( 'gotoaddr', '0' );
		$erraddr = $params->get( 'erraddr', 'Address ## not found!' );
		$erraddr = htmlspecialchars_decode($erraddr, ENT_NOQUOTES);
		$txtaddr = $params->get( 'txtaddr', 'Address: <br />##' );
		$txtaddr = htmlspecialchars_decode($txtaddr, ENT_NOQUOTES);
		$txtaddr = str_replace(array("\r\n", "\r", "\n"), '', $txtaddr );
		$align = $params->get( 'align', 'center' );
		$langtype = $params->get( 'langtype', '' );
		$dir = $params->get( 'dir', '0' );
		$txt_get_dir = $params->get( 'txtgetdir', 'Get Directions' );
		$txt_get_dir = htmlspecialchars_decode($txt_get_dir, ENT_NOQUOTES);
		$txt_from = $params->get( 'txtfrom', 'From here' );
		$txt_from = htmlspecialchars_decode($txt_from, ENT_NOQUOTES);
		$txt_to = $params->get( 'txtto', 'To here' );
		$txt_to = htmlspecialchars_decode($txt_to, ENT_NOQUOTES);
		$txt_diraddr = $params->get( 'txtdiraddr', 'Address: ' );
		$txt_diraddr = htmlspecialchars_decode($txt_diraddr, ENT_NOQUOTES);
		$txt_dir = $params->get( 'txtdir', 'Directions: ' );
		$txt_dir = htmlspecialchars_decode($txt_dir, ENT_NOQUOTES);

		// Key should be filled in the administrtor part or as parameter with the plugin out of content item
		$key = $params->get( 'Google_API_key', '' );
		if ($key=='')
		{
			$multikey = $params->get( 'Google_Multi_API_key', '' );
            $replace = array("\n", "\r", "<br/>", "<br />", "<br>");
			$sites = preg_split("/[\n\r]+/", $multikey);

			foreach($sites as $site)
			{
				$values = explode(";",$site, 2);
                                $values[0] = trim(str_replace($replace, '', $values[0]));
                                $values[1] = str_replace($replace, '', $values[1]);
				if (trim($mosConfig_live_site)==$values[0] ||$mosConfig_live_site=="http://".$values[0]) 
				{
					$key = trim($values[1]);
					break;
				}
			}
			
		}

	    // get default lang from $mosConfig_locale
		if ($langtype == 'site') 
		{
	    	$locale_parts = explode('_', $mosConfig_locale);
		    $lang = $locale_parts[0];
		} else if ($langtype == 'config') 
		{
			$lang = $params->get( 'lang', '' );
		} else {
			$lang = '';
		} 

		// Next parameters can be set as default out of the administrtor module or stay empty and the plugin-code decides the default. 
		$zoomType = $params->get( 'zoomType', '' );
		$mapType = $params->get( 'mapType', '' );

		// default empty and should be filled as a parameter with the plugin out of the content item
		$text='';
		$tooltip='';
		$address='';
		$kml='';
		$client_geo = 0;

		// Give the map a random name so it won't interfere with another map
		$mapnm = randomkeys(5);

		$mosmap=$matches[0][$counter][0];

		if (!$published )
		{
			$row->text = str_replace($mosmap, $code, $row->text);
		} else
		{
			//track if coordinates different from config
			$inline_coords = 0;

			// Match the field details to build the html
			preg_match($singleregex,$mosmap,$mosmapparsed);

			$fields = explode("|", $mosmapparsed[2]);

			foreach($fields as $value)
			{
				$values = explode("=",$value, 2);
				$values=preg_replace("/^'/", '', $values);
				$values=preg_replace("/'$/", '', $values);
				$values=preg_replace("/^&#39;/",'',$values);
				$values=preg_replace("/&#39;$/",'',$values);

				if($values[0]=='debug'){
					$debug_plugin=$values[1];
				}else if($values[0]=='width'){
					$width=$values[1];
				}else if($values[0]=='height'){
					$height=$values[1];
				}else if($values[0]=='lat'){
					$latitude=$values[1];
					$inline_coords = 1;
				}else if($values[0]=='lon'){
					$longitude=$values[1];
					$inline_coords = 1;
				}else if($values[0]=='zoom'){
					$zoom=19-$values[1];
				}else if($values[0]=='key'){
					$key=$values[1];
				}else if($values[0]=='zoomType'){
					$zoomType=$values[1];
				}else if($values[0]=='text'){
					$text=trim($values[1]);
					$text=str_replace("\"",'\'', $text);
				}else if($values[0]=='tooltip'){
					$tooltip=trim($values[1]);
				}else if($values[0]=='mapType'){
					$mapType=$values[1];
				}else if($values[0]=='showMaptype'){
					$showmaptype=$values[1];
				}else if($values[0]=='zoomNew'){
					$zoom_new=$values[1];
				}else if($values[0]=='zoomWheel'){
					$zoom_wheel=$values[1];
				}else if($values[0]=='overview'){
					$overview=$values[1];
				}else if($values[0]=='marker'){
					$marker=$values[1];
				}else if($values[0]=='address'){
					$address=trim($values[1]);
				}else if($values[0]=='gotoaddr'){
					$gotoaddr=$values[1];
				}else if($values[0]=='align'){
					$align=$values[1];
				}else if($values[0]=='lang'){
					$lang=$values[1];
				}else if($values[0]=='kml'){
					$kml=$values[1];
				}else if($values[0]=='dir'){
					$dir=$values[1];
				}
			}

			debug_log("Plugin Google Maps version 2.9");
			debug_log("Parameters: ");
			debug_log("- debug: ".$debug_plugin);
			debug_log("- dir: ".$dir);
			debug_log("- text: ".$text);
			
			if($inline_coords == 0 && !empty($address))
			{
				$coord = get_geo($address, $key);
				list ($longitude, $latitude, $altitude) = explode(",", $coord);
				$inline_coords = 1;
				if ($coord=='')
					$client_geo = 1;
			}

			if (is_numeric($width))
			{
				$width .= "px";
			}
			if (is_numeric($height))
			{
				$height .= "px";
			}

			$code="";

			// Generate the map position prior to any Google Scripts so that these can parse the code
			$code.= "<!-- fail nicely if the browser has no Javascript -->
					<noscript><b>JavaScript must be enabled in order for you to use Google Maps.</b> <br/>
						  However, it seems JavaScript is either disabled or not supported by your browser. <br/>
					      To view Google Maps, enable JavaScript by changing your browser options, and then try again. 
				    </noscript>";
			$code.="<div align=\"".$align."\">";
					
			if ($gotoaddr=='1')
			{
				$code.="<form name=\"gotoaddress".$id."_".$mapnm."_".$counter."\" class=\"gotoaddress\" action=\"javascript:gotoAddress".$id."_".$mapnm."_".$counter."();\">";
				$code.="	<input id=\"txtAddress".$id."_".$mapnm."_".$counter."\" name=\"txtAddress".$id."_".$mapnm."_".$counter."\" type=\"text\" size=\"25\" value=\"\">";
				$code.="	<input name=\"goto\" type=\"button\" class=\"button\" onClick=\"gotoAddress".$id."_".$mapnm."_".$counter."();return false;\" value=\"Goto\">";
				$code.="</form>";
			}

			$code.="<div id=\"googlemap".$id."_".$mapnm."_".$counter."\" style=\"width:".$width."; height:".$height."\"></div>";
			$code.="</div>";

			// Only add the google javascript once
			if($first)
			{
				if ($lang!='')
					$mainframe->addCustomHeadTag( "<script src=\"http://maps.google.com/maps?file=api&amp;v=2.x&amp;hl=".$lang."&amp;key=".$key."\" type=\"text/javascript\"></script>");
				else
					$mainframe->addCustomHeadTag( "<script src=\"http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=".$key."\" type=\"text/javascript\"></script>");

				$first=false;
			}

			$code.="<script type='text/javascript'>//<![CDATA[\n";

			// Globale map variable linked to the div
			$code.="var tst".$id."_".$mapnm."_".$counter."=document.getElementById('googlemap".$id."_".$mapnm."_".$counter."');
			var tstint".$id."_".$mapnm."_".$counter.";
			var map".$id."_".$mapnm."_".$counter.";
			";

			if ( strpos(" ".$_SERVER['HTTP_USER_AGENT'], 'Opera') )
			{
				$code.="var _mSvgForced = true;
						var _mSvgEnabled = true; ";
			}

			if($zoom_wheel=='1')
			{
				$code.="function CancelEvent".$id."_".$mapnm."_".$counter."(event) { 
					        var e = event; 
					        if (typeof e.preventDefault == 'function') e.preventDefault(); 
						        if (typeof e.stopPropagation == 'function') e.stopPropagation(); 
	
					        if (window.event) { 
				                window.event.cancelBubble = true; // for IE 
				                window.event.returnValue = false; // for IE 
					        } 
						}
					";
			}

			if ($gotoaddr=='1')
			{
				$code.="function gotoAddress".$id."_".$mapnm."_".$counter."() {
							var address = document.getElementById('txtAddress".$id."_".$mapnm."_".$counter."').value;

							if (address.length > 0) {
							    var geocoder = new GClientGeocoder();

							    geocoder.getLatLng(address,
							    function(point) {
							        if (!point) {
										var erraddr = '{$erraddr}';
										erraddr = erraddr.replace(/##/, address);
							          alert(erraddr);
							        } else {
									  var txtaddr = '{$txtaddr}';
									  txtaddr = txtaddr.replace(/##/, address);
							          map".$id."_".$mapnm."_".$counter.".setCenter(point);
									  map".$id."_".$mapnm."_".$counter.".openInfoWindowHtml(point,txtaddr);
									  setTimeout('map".$id."_".$mapnm."_".$counter.".closeInfoWindow();', 5000);
							        }
							      });
							  }
						}";
			}

			if ($dir=='1') {
				$code.="\nDirectionMarkersubmit".$id."_".$mapnm."_".$counter." = function( formObj ){
							if(formObj.dir[1].checked ){
								tmp = formObj.daddr.value;
								formObj.daddr.value = formObj.saddr.value;
								formObj.saddr.value = tmp;
							}
							formObj.submit();
							if(formObj.dir[1].checked ){
								tmp = formObj.daddr.value;
								formObj.daddr.value = formObj.saddr.value;
								formObj.saddr.value = tmp;
							}
						}";
			}

			// Functions to wacth if the map has changed
			$code.="\nfunction checkMap".$id."_".$mapnm."_".$counter."()
			{
				if (tst".$id."_".$mapnm."_".$counter.")
					if (tst".$id."_".$mapnm."_".$counter.".offsetWidth != tst".$id."_".$mapnm."_".$counter.".getAttribute(\"oldValue\"))
					{
						tst".$id."_".$mapnm."_".$counter.".setAttribute(\"oldValue\",tst".$id."_".$mapnm."_".$counter.".offsetWidth);

						if (tst".$id."_".$mapnm."_".$counter.".getAttribute(\"refreshMap\")==0)
							if (tst".$id."_".$mapnm."_".$counter.".offsetWidth > 0) {
								clearInterval(tstint".$id."_".$mapnm."_".$counter.");
								getMap".$id."_".$mapnm."_".$counter."();
								tst".$id."_".$mapnm."_".$counter.".setAttribute(\"refreshMap\", 1);
							} 
					}
			}
			";

			// Function for displaying the map and marker
			$code.="	function getMap".$id."_".$mapnm."_".$counter."(){
				if (tst".$id."_".$mapnm."_".$counter.".offsetWidth > 0) {
					map".$id."_".$mapnm."_".$counter." = new GMap2(document.getElementById('googlemap".$id."_".$mapnm."_".$counter."'));
					map".$id."_".$mapnm."_".$counter.".getContainer().style.overflow='hidden'; ;
					";

					if($zoomType!='None')
					{
						if($zoomType=='Large')
						{
							$code.="map".$id."_".$mapnm."_".$counter.".addControl(new GLargeMapControl());";
						} else
						{
							$code.="map".$id."_".$mapnm."_".$counter.".addControl(new GSmallMapControl());";
						}
					} 

					if(!$overview==0)
					{
						$code.="var overviewmap = new GOverviewMapControl();";
						$code.="map".$id."_".$mapnm."_".$counter.".addControl(overviewmap, new GControlPosition(G_ANCHOR_BOTTOM_RIGHT));";
						
						if($overview==2)
						{
							$code.="overviewmap.hide(true);";
						}
					}

					if($showmaptype!='0')
					{
						$code.="map".$id."_".$mapnm."_".$counter.".addControl(new GMapTypeControl());";
					} 

					if($client_geo == 1)
					{
						$code.="var geocoder = new GClientGeocoder();";
						$replace = array("\n", "\r", "&lt;br/&gt;", "&lt;br /&gt;", "&lt;br&gt;");
						$addr = str_replace($replace, '', $address);

						$code.="var address = '".$addr."';";
						$code.="geocoder.getLatLng(address, function(point) {
							        if (point) {";
					} else { 
						$code.="var point = new GLatLng( $latitude, $longitude);";
					}

					if ($inline_coords == 0 && !empty($kml))
						$code.="map".$id."_".$mapnm."_".$counter.".setCenter(new GLatLng(0, 0), 0);";					
					else
						$code.="map".$id."_".$mapnm."_".$counter.".setCenter(point, ".$zoom.");";					
						
					if ($kml!='') {
						$code .= "var xml = new GGeoXml(\"".$kml."\");";
						$code .= "map".$id."_".$mapnm."_".$counter.".addOverlay(xml);";
						if (!$overview==0) {
							$code .= "var xml2 = new GGeoXml(\"".$kml."\");";
							$code .= "overview = overviewmap.getOverviewMap();";
							$code .= "overview.addOverlay(xml2);";
						}
						if ($inline_coords==0||($inline_coords==1&&empty($text)))
							$code .= "xml.gotoDefaultViewport(map".$id."_".$mapnm."_".$counter.");";
					}

					if($mapType=='Satellite')
					{
						$code.="map".$id."_".$mapnm."_".$counter.".setMapType(G_SATELLITE_MAP);";
					} else
					{
						if($mapType=='Hybrid')
						{
							$code.="map".$id."_".$mapnm."_".$counter.".setMapType(G_HYBRID_MAP);";
						} else
						{
							$code.="map".$id."_".$mapnm."_".$counter.".setMapType(G_NORMAL_MAP);";
						}
					}

					if($zoom_new=='1')
					{
						$code.="
						map".$id."_".$mapnm."_".$counter.".enableContinuousZoom();
						map".$id."_".$mapnm."_".$counter.".enableDoubleClickZoom();
						";
					} else {
						$code.="
						map".$id."_".$mapnm."_".$counter.".disableContinuousZoom();
						map".$id."_".$mapnm."_".$counter.".disableDoubleClickZoom();
						";
					}

					if($zoom_wheel=='1')
					{
						$code.="map".$id."_".$mapnm."_".$counter.".enableScrollWheelZoom();
						";
					} 


					if (($inline_coords == 1&&!(!empty($kml)&&$text==''&&$dir==0))||($inline_coords == 0 && empty($kml))) {

//					if (($inline_coords == 1&&($text!=''||$address!=''))||($inline_coords == 0 && empty($kml))) {

						if ($tooltip!='') 
							$code.="var marker".$id."_".$mapnm."_".$counter." = new GMarker(point, {title:\"".$tooltip."\"});";
						else
							$code.="var marker".$id."_".$mapnm."_".$counter." = new GMarker(point);";
						
						$code.="map".$id."_".$mapnm."_".$counter.".addOverlay(marker".$id."_".$mapnm."_".$counter.");
						";

						if ($text!=''||$dir==1) {

							if ($dir=='1') {
									$dirform="<form action='http://maps.google.com/maps' method='get' target='_blank' onsubmit='DirectionMarkersubmit".$id."_".$mapnm."_".$counter."(this);return false;' class='mapdirform'>";
							    $dirform.="<br />".$txt_dir."<input type='radio' checked name='dir' value='to'> <b>".$txt_to."</b> <input type='radio' name='dir' value='from'><b>".$txt_from."</b>";
							    $dirform.="<br />".$txt_diraddr."<input type='text' class='inputbox' size='20' name='saddr' id='saddr' value='' /><br />";
							    $dirform.="<input value='".$txt_get_dir."' class='button' type='submit' style='margin-top: 2px;'>";
								
								if (!empty($address))
								    $dirform.="<input type='hidden' name='daddr' value='".$address." (".$latitude.", ".$longitude.")'/></form>";
								else
								    $dirform.="<input type='hidden' name='daddr' value='".$latitude.", ".$longitude."'/></form>";
								// Add form before div or at the end of the html.
								$pat="/&lt;\/div&gt;$/";
								if (preg_match($pat, $text))
									$text = preg_replace($pat, $dirform."</div>", $text);
								else
									$text.=$dirform;
							}
							
							$text = htmlspecialchars_decode($text, ENT_NOQUOTES);

							// If marker 
							if ($marker==1)
								$code.="marker".$id."_".$mapnm."_".$counter.".openInfoWindowHtml(\"".$text."\");";
							
							$code.="GEvent.addListener(marker".$id."_".$mapnm."_".$counter.", 'click', function() {
									marker".$id."_".$mapnm."_".$counter.".openInfoWindowHtml(\"".$text."\");
									});
							";
						}
					}
					
					if($zoom_wheel=='1')
					{
						$code.="GEvent.addDomListener(tst".$id."_".$mapnm."_".$counter.", 'DOMMouseScroll', CancelEvent".$id."_".$mapnm."_".$counter.");
								GEvent.addDomListener(tst".$id."_".$mapnm."_".$counter.", 'mousewheel', CancelEvent".$id."_".$mapnm."_".$counter.");
							";
					}

					if($client_geo == 1)
					{
						$code.="		        }
								      });";
					}		
					// End of script voor showing the map 
					$code.="}
		}
		//]]></script>
		";

		// Call the Maps through timeout to render in IE also
		// Set an event for watching the changing of the map so it can refresh itself
		$code.= "<script type=\"text/javascript\">//<![CDATA[
			    if (GBrowserIsCompatible()) {
					tst".$id."_".$mapnm."_".$counter.".setAttribute(\"oldValue\",0);
					tst".$id."_".$mapnm."_".$counter.".setAttribute(\"refreshMap\",0);
					tstint".$id."_".$mapnm."_".$counter."=setInterval(\"checkMap".$id."_".$mapnm."_".$counter."()\",500);
				}
		//]]></script>
		";

		if ($debug_text!='')
			$code ="\n<!-- ".$debug_text."\n-->\n".$code;
			
		$row->text = preg_replace($regex, $code, $row->text, 1);
		} 

	}

	return true;
}

?>
