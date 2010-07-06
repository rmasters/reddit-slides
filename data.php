<?php
/**
 * Pulls the JSON feed from a subreddit and outputs a simpler JSON feed with
 * image links.
 * @author      Ross Masters <ross@php.net>
 * @package     RedditSlides
 * @license     GNU GPLv3
 * @version     1.0
 * @todo        Output errors as json
 * @todo        Caching (in memory or file)
 * @todo        Inform the reddit site who we are using a user-agent
 * @todo        If the link is a page, and it contains an image try to use it
 */

/**
 * Sort out where we will retrieve the feed from. By default use /r/pics.
 * @todo        Custom subreddit urls
 * @todo        Simpler ?r=pics query for using a subreddit from reddit.com
 */

// Default subreddit

// If a custom url is used (e.g. for reddits other than reddit.com) then
// we clean it up and try to match partial urls.
if (isset($_GET["url"])) {
    $_GET["url"] = urldecode($_GET["url"]);
    
    // Check it is a valid url
    if (filter_var($_GET["url"], FILTER_VALIDATE_URL)) {
        $url = $_GET["url"];
        
        // Trim ".json" for now
        if (substr($_GET["url"], -5, 5) == ".json") {
            $url = substr($_GET["url"], 0, -5);
        }
    }
} elseif (isset($_GET["r"])) {
	$url = "http://reddit.com/r/" . $_GET["r"] . "/";
} else {
    $url = "http://reddit.com/r/pics/";
}

$url .= "/.json?count=25";
        
if (isset($_GET["after"])) {
    $url .= "&after=" . $_GET["after"];
}

// Attempt to get the json data
$json = file_get_contents($url);
if (!$json) {
    die("Couldn't load <code>$url</code>.");
}

/**
 * Build our own smaller JSON for the slideshow, with only the information we
 * really need.
 */
$jsonData = json_decode($json);
$links = array();

// For each child (link) we check it is not a self (text-only) post, or a 
// non-image, and add it to a collection.
$urlParts = parse_url($url);
$domain = "http://" . $urlParts["host"];
foreach ($jsonData->data->children as $child) {
    $link = $child->data;
    
    if ($link->is_self) {
        continue;
    }
    
    if ($link->domain == "imgur.com") {
    	if (!in_array(substr($link->url, -3, 3), array("jpg", "png", "gif"))) {
    		$link->url .= ".png";
    	}
    }
    
    $links[] = array(
        "url" => $link->url,
        "title" => $link->title,
        "id" => $link->id,
        "author" => $link->author,
        "link" => $domain . $link->permalink,
        "reddit_id" => $link->name
    );
}

// Build our output array, include some metadata like when this was generated.
$output = array(
    "generated" => date("r")
);
$output["images"] = $links;

$jsonOutput = json_encode($output);

// If a callback is specified wrap it in a function
// @todo    Verify callback name is a valid javascript function name
if (isset($_GET["callback"])) {
    echo $_GET["callback"] . "($jsonOutput)";
} else {
    echo $jsonOutput;
}
