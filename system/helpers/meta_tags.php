<?php  if (!defined('SYSPATH')) exit('No direct script access allowed');
/**
 * Construct Meta Tags for Header
 *
 * Accepts one paramter
 *
 * @access	public
 * @param	string the value of the page view
 * @return	hash
 */
if (! function_exists('meta_tags'))
{
	function meta_tags($page_view)
	{
		/**
		* @desc title:
		* max length 64 characters
		* Hint: Search engines use the page title as the listing name for their results. 
		* 
		* Search engines tend to place a lot of importance on the title tag, for obvious reasons - the title is supposed to describe the whole page in
		* a few words. Therefore it's very important to get the title tag right if you want good search engine results. As always though, your primary
		* goal should be relevance and usability for real people while using some keywords. Title tags also appears in the browser title bar and is the
		* default name if visitors adds your page to their favorites. 
		*
		* 
		* @desc description:
		* max length 150 characters
		* Hint: Avoid repeating keywords more than 3-7 times in your meta description. Some search engines consider it to be spam.
		* 
		* Search engines that support META tags will often display the Description META tag along with your title in their results. Search engines will
		* often capture the entire META tag of your description field, but webmasters should bear in mind that when a search engine displays the results
		* to a user, the space is limited, usually under 20 words which you can use to grab the attention of a user. For this reason, when creating your
		* META tags, webmasters should make the first sentence of their description field to capture the attention of a user and use the rest of the
		* description tag to elaborate further.
		* 
		* 
		* @desc keywords:
		* max length 75 characters
		* Hint: Avoid repeating keywords more than 3-7 times. Up to 3 repetitions are recommended -- just don't place them one after the other.
		* 
		* Search engines that support META tags will often use the keywords found on your pages as a means to categorize your website based on the search
		* engines indexing algorithms (proprietary algorithms which index your website in search engine databases). Ensure you choose keywords that are
		* relevant to your site and avoid excessive repetition as many search engines will penalize your rankings for attempting to abuse their system.
		* Similar to the Description META Tag, search engines give priority to the first few words in your description, so focus on your main keywords and
		* then elaborate further by using synonyms or other related words.
		* 
		* 
		* @desc robots:
		* Valid values for the "CONTENT" attribute are: "INDEX", "NOINDEX", "FOLLOW", "NOFOLLOW". Multiple comma-separated values are allowed, but obviously
		* only some combinations make sense. If there is no robots <META> tag, the default is "INDEX,FOLLOW", so there's no need to spell that out. That leaves:
		*/
		$data = array(
			'home/index' => array(
						'My Title',
						'My Description',
						'My Keywords',
						'index,follow'
					),
		);
		if(isset($data[$page_view]))
		{
			return $data[$page_view];
		}
		else
		{
			return $data['home/index'];
		}
	}
}