<?php

/**
 * @package pickr-poll-embedder
 * @version 1.1
 */
/*
Plugin Name: Pickr Poll Embedder
Description: Pickr embed plugin for WordPress makes everything easier while embedding polls to your posts. And it provides the basic features to customize the appearance.

All you have to do is simply copy and paste an unformatted Pickr poll link and enclose it with [pickr] shortcode. Your poll embed will then show up when you preview or publish the post. Shortcode has some options that you can apply to your embedded Pickr poll.

Simply install the plugin and follow these instructions:

* Choose the poll you want to embed into your post. Paste its URL on its own line.
* Wrap the URL with [pickr] shortcode. Example: [pickr]https://pickr.us/#!/poll/437[/pickr]
* If you would like to customize it, here are the options you have:
    - controls  -  Set this 'no' to hide the control bar containing sharing options and stats. Example: [pickr controls=no]https://pickr.us/#!/poll/437[/pickr]
    - tags  -  Set this to 'no' to hide the answer hashtags. Example: [pickr tags=no]https://pickr.us/#!/poll/437[/pickr]
    - poller  -  Set this to 'no' to hide the username of the poller. Example: [pickr poller=no]https://pickr.us/#!/poll/437[/pickr]

You can combine these three options as you like. 
Author: SRDC
Author URI: http://srdc.com.tr/dist/
Version: 1.1
License: GPLv3
*/

function pickrPollEmbedder($text) {
	wp_enqueue_script( 'iframeResizer.js', plugins_url('iframeResizer.js' , __FILE__ ), false);
	preg_match_all("|(\[pickr([A-Za-z0-9\?=& ]*)\])((http(s)*://)*)pickr.us/#!/poll/([0-9][A-Za-z0-9\?=]*)(\[/pickr\])|", $text, $fullLink, PREG_PATTERN_ORDER);
	foreach ($fullLink[0] as $link)
	{
		$controls = 0;
		$tags = 0;
		$poller = 0;

		$tempLink = str_replace('poll', 'embed', $link);
		$tempLink = str_replace(']pickr.us', ']https://pickr.us', $tempLink);
		$tempLink = str_replace('http:', 'https:', $tempLink);
		$tempLink = preg_replace("|(\[pickr(([A-Za-z0-9\?=& ])*)\])|", "", $tempLink);
		$tempLink = str_replace("[/pickr]", "", $tempLink);
		if (substr($tempLink, -1) != '?' || substr($tempLink, -1) != 'e')
		{
			$tempLink = $tempLink . '?';
		}

		preg_match("|(\[pickr(([A-Za-z0-9\?=& ])*)\])|", $link, $parameters, PREG_OFFSET_CAPTURE);
		$returnValue = preg_split('" "', $parameters[0][0], -1);

		preg_match("|controls[ ]*=[ ]*no|", $parameters[0][0], $checkValue, PREG_OFFSET_CAPTURE);
		if ($checkValue && $checkValue[0] && $checkValue[0][0])
			$controls = 1;
		preg_match("|tags[ ]*=[ ]*no|", $parameters[0][0], $checkValue, PREG_OFFSET_CAPTURE);
		if ($checkValue && $checkValue[0] && $checkValue[0][0])
			$tags = 1;
		preg_match("|poller[ ]*=[ ]*no|", $parameters[0][0], $checkValue, PREG_OFFSET_CAPTURE);
		if ($checkValue && $checkValue[0] && $checkValue[0][0])
			$poller = 1;
		
		if ($controls == 1)
			$tempLink = $tempLink . "&hideControls=" . $controls;
		if ($tags == 1)
			$tempLink = $tempLink . "&hideOptionTags=" . $tags;
		if ($poller == 1)
			$tempLink = $tempLink . "&hidePoller=" . $poller;

		$currentSite = get_site_url();
		$currentSite = preg_replace("|(http(s)*://)*(www.)*|", "", $currentSite);
		$tempLink = $tempLink . "&rp=3&ri=" . $currentSite;

		$text = str_replace($link, '<iframe frameborder="0" scrolling="no" width="100%" src="' . $tempLink . '" seamless></iframe>', $text);
	}
	wp_enqueue_script('heightJS', plugins_url( 'pollHeight.js' , __FILE__ ), false);
      	return $text;
}
add_filter('the_content', 'pickrPollEmbedder');
?>
