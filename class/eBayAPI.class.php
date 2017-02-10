<?php

/**
* 
*/

class EbayAPI {
	public $appid;
	public $globalid = "EBAY-US";
	public $version = "1.0.0";
	public $endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';

	private $categoryID = array(
		0 => "All Categories",
		20081 => "Antiques",
		550 => "Art",
		2984 => "Baby",
		267 => "Books",
		12576 => "Business & Industrial",
		625 => "Cameras & Photo",
		15032 => "Cell Phones & Accessories",
		11450 => "Clothing, Shoes & Accessories",
		11116 => "Coins & Paper Money",
		1 => "Collectibles",
		58058 => "Computers/Tablets & Networking",
		293 => "Consumer Electronics",
		14339 => "Crafts",
		237 => "Dolls & Bears",
		11232 => "DVDs & Movies",
		6000 => "eBay Motors",
		45100 => "Entertainment Memorabilia",
		172008 => "Gift Cards & Coupons",
		26395 => "Health & Beauty",
		11700 => "Home & Garden",
		281 => "Jewelry & Watches",
		11233 => "Music",
		619 => "Musical Instruments & Gear",
		1281 => "Pet Supplies",
		870 => "Pottery & Glass",
		10542 => "Real Estate",
		316 => "Specialty Services",
		888 => "Sporting Goods",
		64482 => "Sports Mem, Cards & Fan Shop",
		260 => "Stamps",
		1305 => "Tickets & Experiences",
		220 => "Toys & Hobbies",
		3252 => "Travel",
		1249 => "Video Games & Consoles",
		99 => "Everything Else",
	);
	private $amazonCategoryConversion = array(
		'apparel' => array("11450"),
		'appliances' => array("11700"),
		'artsandcrafts' => array("550"),
		'automotive' => array("6000"),
		'baby' => array("2984"),
		'beauty' => array("26395"),
		'books' => array("267"),
		'classical' => array("20081"),
		'collectibles' => array("1"),
		'digitalmusic' => array("58058"),
		'dvd' => array("11232"),
		'electronics' => array("293"),
		'foreignbooks' => array("267"),
		'garden' => array("11700"),
		'gourmetfood' => array("11700"),
		'grocery' => array("11700"),
		'healthpersonalcare' => array("26395"),
		'hobbies' => array("220"),
		'home' => array("11700"),
		'homegarden' => array("11700"),
		'homeimprovement' => array("11700"),
		'industrial' => array("12576"),
		'jewelry' => array("281"),
		'kindlestore' => array("267"),
		'kitchen' => array("11700"),
		'lighting' => array("11700"),
		'magazines' => array("267"),
		'miscellaneous' => array("99"),
		'mobileapps' => array("58058"),
		'mp3downloads' => array("58058"),
		'music' => array("11233"),
		'musicalinstruments' => array("619"),
		'officeproducts' => array("12576"),
		'outdoorliving' => array("11700"),
		'pchardware' => array("58058"),
		'petsupplies' => array("1281"),
		'photo' => array("625"),
		'shoes' => array("11450"),
		'software' => array("58058"),
		'softwarevideogames' => array("1249"),
		'sportinggoods' => array("888"),
		'tools' => array("11700"),
		'toys' => array("220"),
		'vhs' => array("11232"),
		'video' => array("11232"),
		'videogames' => array("1249"),
		'watch' => array("281"),
		'wireless' => array("15032"),
		'wirelessaccessories' => array("15032"),
	);

	public function __construct($appid) {
		$this->appid = $appid;
	}
	public function getListings($query, $limit = 5, $category) {
		$categoryarray = isset($this->amazonCategoryConversion[str_replace(" ", "", $category)]) ? $this->amazonCategoryConversion[str_replace(" ", "", $category)] : array("0");
		$safequery = urlencode($query);
	
		$apicall = $this->endpoint."?";
		$apicall .= "OPERATION-NAME=findItemsAdvanced";
		$apicall .= "&SERVICE-VERSION=".$this->version;
		$apicall .= "&SECURITY-APPNAME=".$this->appid;
		$apicall .= "&GLOBAL-ID=".$this->globalid;
		$apicall .= "&keywords=$safequery";
		$apicall .= "&paginationInput.entriesPerPage=$limit";
		$apicall .= "&categoryId=".implode("&categoryId=", $categoryarray);
		$apicall .= "&affiliate.trackingId=5337817621&affiliate.networkId=9";

		$resp = simplexml_load_file($apicall);

		$results = array();
		if ($resp->ack == "Success") {
			$total = json_decode(json_encode($resp->paginationOutput->totalEntries),1);
			$results['TotalEbay'] = $total[0];
			foreach($resp->searchResult->item as $item) {
				$item = json_decode(json_encode($item),1);
				$results[] = array(
					"ID" => $item['itemId'],
					"Name" => $item['title'],
					"GlobalTitle" => $item['globalId'],
					"Condition" => isset($item['condition']['conditionDisplayName']) ? $item['condition']['conditionDisplayName'] : "",
					"Price" => $item['sellingStatus']['currentPrice'],
					"URL" => $item['viewItemURL'],
					"ImageURL" => isset($item['galleryURL']) ? $item['galleryURL'] : "",
					"PrimaryCategory" => $item['primaryCategory']['categoryName'],
					"SecondaryCategory" => isset($item['secondaryCategory']['categoryName']) ? $item['secondaryCategory']['categoryName'] : "",
					"TopRatedListing" => $item['topRatedListing'],
				);
			}
		} else {
			$results['TotalEbay'] = 0;
			$error = json_decode(json_encode($resp->errorMessage->error),1);
			$results['error'] = array(
				"ID"=>$error['errorId'],
				"Message"=>$error['message'],
			);
		}
		return $results;
	}
}

?>
