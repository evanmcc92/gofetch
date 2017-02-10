<?php

/**
* 
*/
require_once 'eBayAPI.class.php';
require_once 'AmazonECS.class.php';
require_once 'predis/autoload.php';
class getItemPrice
{

	private $config;
	private $ebayWeight = array(
		// Business & Industrial, Cameras & Photo, Cell Phones & Accessories, Computers, Tablets & Networking, Electronics, Home & Garden, Musical Instruments & Gear
		"New" => 1,
		"New other (see details)" => 0.9,
		"Manufacturer refurbished" => 0.7,

		"Seller refurbished" => 0.6,
		"Used" => 0.4,
		"For parts or not working" => 0.2,
		// Clothing, Shoes & Accessories: Clothing and accessories, Jewelry & Watches
		"New with tags" => 1,
		"New without tags" => 0.9,
		"New with defects" => 0.8,
		"Pre-owned" => 0.7,
		// Clothing, Shoes & Accessories: Shoes
		"New with box" => 1,
		"New without box" => 0.9,
		"New with defects" => 0.8,
		"Pre-owned" => 0.7,
		// Clothing, Shoes & Accessories: Underwear
		"New with tags" => 1,
		"New without tags" => 0.9,
		"New with defects" => 0.8,
		// Collectibles, Crafts, Dolls & Bears, Pet Supplies, Toys & Hobbies
		"New" => 1,
		"Used" => 0.4,
		// Books
		"Brand New" => 1,
		"Like New" => 0.9,
		"Very Good" => 0.7,
		"Good" => 0.5,
		"Acceptable" => 0.3,
		// DVDs & Movies, Music, Video Games
		"Brand New" => 1,
		"Like New" => 0.9,
		"Very Good" => 0.7,
		"Good" => 0.5,
		"Acceptable" => 0.3,
		// Motors: Parts & Accessories
		"New" => 1,
		"New other (see details)" => 1,
		"Manufacturer refurbished" => 0.9,
		"Seller refurbished" => 0.9,
		"For parts or not working" => 0.3,
		// Tires
		"New" => 1,
		"Certified pre-owned" => 0.9,
		"Used" => 0.4,
		// Baby, Health & Beauty, Sporting Goods
		"New" => 1,
		"New other (see details)" => 0.9,
		"Used" => 0.4,
		"For parts or not working" => 0.2,
		// Food & Beverages
		"New" => 1,
	);
	private $redis;
	function __construct($config){
		Predis\Autoloader::register();

		$this->config = $config;
		$this->redis = new Predis\Client(array(
			'host' => $this->config['redis']['host'], 
			'port' => $this->config['redis']['port'], 
			'password' => $this->config['redis']['password'], 
		));
	}
	function getItemDetails($ASIN) {
		$result = array();
		$redisresult = $this->redis->get($ASIN);
		// check if it is in redis
		if (isset($redisresult) && $redisresult !== "") {
			$result = json_decode($redisresult,1);
		} else {
			// if no redis entry then use api
			try {
				$amazonEcs = new AmazonECS($this->config['amazon']['api-key'], $this->config['amazon']['api-secret'], "com", $this->config['amazon']['associate-tag']);
				$response = $amazonEcs->category("All")->responseGroup('Offers,VariationSummary,Large')->lookup($ASIN);
				$key = $response->Items->Item;
				$object = json_decode(json_encode($key),1);
				$ThumbnailImage = "";
				if (isset($object['ThumbnailImage']['URL'])) {
					$ThumbnailImage = $object['ThumbnailImage']['URL'];
				} elseif (isset($object['ImageSets']['ImageSet']['ThumbnailImage']['URL'])) {
					$ThumbnailImage = $object['ImageSets']['ImageSet']['ThumbnailImage']['URL'];
				}
				
				$result = array(
					"ASIN"=>$object['ASIN'],
					"DetailPageURL"=>$object['DetailPageURL'],
					"ItemManufacturer"=>$object['ItemAttributes']['Manufacturer'],
					"Title"=>$object['ItemAttributes']['Title'],
					"Artist"=>"",
					"Category"=>$object['ItemAttributes']['ProductGroup'],
					"Brand"=>isset($object['ItemAttributes']['Brand']) ? $object['ItemAttributes']['Brand'] : "",
					"Binding"=>$object['ItemAttributes']['Binding'],
					"UPC"=>isset($object['ItemAttributes']['UPC']) ? $object['ItemAttributes']['UPC'] : "",
					"ImageSmall"=>(isset($object['SmallImage']['URL'])) ? $object['SmallImage']['URL'] : $object['ImageSets']['ImageSet']['SmallImage']['URL'],
					"ImageThumb"=>$ThumbnailImage,
					"ImageMedium"=>(isset($object['MediumImage']['URL'])) ? $object['MediumImage']['URL'] : $object['ImageSets']['ImageSet']['MediumImage']['URL'],
					"ImageLarge"=>(isset($object['LargeImage']['URL'])) ? $object['LargeImage']['URL'] : $object['ImageSets']['ImageSet']['LargeImage']['URL'],
					"TotalItems" => 0,
					"ListPrice" => 0,
					"LowestPriceNew"=>isset($object['OfferSummary']['LowestNewPrice']['FormattedPrice']) ? $object['OfferSummary']['LowestNewPrice']['FormattedPrice'] : 0,
					"LowestPriceUsed"=>isset($object['OfferSummary']['LowestUsedPrice']['FormattedPrice']) ? $object['OfferSummary']['LowestUsedPrice']['FormattedPrice'] : 0,
					"LowestPriceRefurbished"=>isset($object['OfferSummary']['LowestRefurbishedPrice']['FormattedPrice']) ? $object['OfferSummary']['LowestRefurbishedPrice']['FormattedPrice'] : 0,
					"LowestPriceCollectible"=>isset($object['OfferSummary']['LowestCollectiblePrice']['FormattedPrice']) ? $object['OfferSummary']['LowestCollectiblePrice']['FormattedPrice'] : 0,
					"LowestPriceNewAmount"=>isset($object['OfferSummary']['LowestNewPrice']['Amount']) ? $object['OfferSummary']['LowestNewPrice']['Amount'] : 0,
					"LowestPriceUsedAmount"=>isset($object['OfferSummary']['LowestUsedPrice']['Amount']) ? $object['OfferSummary']['LowestUsedPrice']['Amount'] : 0,
					"LowestPriceRefurbishedAmount"=>isset($object['OfferSummary']['LowestRefurbishedPrice']['Amount']) ? $object['OfferSummary']['LowestRefurbishedPrice']['Amount'] : 0,
					"LowestPriceCollectibleAmount"=>isset($object['OfferSummary']['LowestCollectiblePrice']['Amount']) ? $object['OfferSummary']['LowestCollectiblePrice']['Amount'] : 0,
					"TotalNew"=>$object['OfferSummary']['TotalNew'],
					"TotalUsed"=>$object['OfferSummary']['TotalUsed'],
					"TotalRefurbished"=>$object['OfferSummary']['TotalRefurbished'],
					"TotalCollectible"=>$object['OfferSummary']['TotalCollectible'],
					"Description"=>"",
					"Features"=>"",
					"WeightedPrice" => 0,
					"SimilarItems" => []
				);
				$key = $response->Items->Item;
				$object = json_decode(json_encode($key),1);
				if (isset($object['SimilarProducts']['SimilarProduct'])) {
					if (isset($object['SimilarProducts']['SimilarProduct'][0])) {
						foreach ($object['SimilarProducts']['SimilarProduct'] as $key) {
							$result["SimilarItems"][] = array(
								"ASIN"=>$key['ASIN'],
								"Title"=>$key['Title'],
							);
						}
					} else {
						$result["SimilarItems"][] = array(
							"ASIN"=>$object['SimilarProducts']['SimilarProduct']['ASIN'],
							"Title"=>$object['SimilarProducts']['SimilarProduct']['Title'],
						);
					}
				}
				if (isset($object['ItemAttributes']['Artist'])){
					if (is_array($object['ItemAttributes']['Artist'])) {
						$result['Artist'] = $object['ItemAttributes']['Artist'][0];
					} else {
						$result['Artist'] = $object['ItemAttributes']['Artist'];
					}
				}

				if (isset($object['ItemAttributes']['ListPrice'])) {
					$result["ListPrice"] = $object['ItemAttributes']['ListPrice']['FormattedPrice'];
					$result["ListPriceAmount"] = $object['ItemAttributes']['ListPrice']['Amount'];
				} else {
					$result["ListPrice"] = 0;
					$result["ListPriceAmount"] =0;
				}

				if (isset($object['ItemAttributes']['Feature'])) {
					if (is_array($object['ItemAttributes']['Feature']) > 0) {
						foreach ($object['ItemAttributes']['Feature'] as $key) {
							$result['Features'] .= "$key ";
						}
					}
				}
				if (isset($object['EditorialReviews']['EditorialReview']['Content'])) {
					$result['Description'] .= $object['EditorialReviews']['EditorialReview']['Content']." ";
				}
				
				foreach ($result as $key => $value) {
					if (stripos($key, "Total")!==false) {
						$result['TotalItems'] += $value; 
					}
				}

				$query = $result['Title'] . " " . $result['Artist'] . " " .$result['Binding'];
				$EbayAPI = new EbayAPI($this->config['ebay']['app-id']);
				$result['eBayResults'] = $EbayAPI->getListings($query, 10, strtolower($result['Category']));

				$result['TotalItems'] = $result['TotalItems'] + $result['eBayResults']['TotalEbay'];

				$ratings[] = ($result['ListPriceAmount'] > 0) ? array("Price" => ($result['ListPriceAmount']*0.01), "Weight"=> ($result['TotalItems']*0.05)) : array("Price" => 0, "Weight" => 0);
				$ratings[] = array("Price" => ($result['LowestPriceNewAmount']*0.01), "Weight"=>$result['TotalNew']);
				$ratings[] = array("Price" => ($result['LowestPriceUsedAmount']*0.01), "Weight"=>($result['TotalUsed']*0.4));
				$ratings[] = array("Price" => ($result['LowestPriceRefurbishedAmount']*0.01), "Weight"=>($result['TotalCollectible']*0.7));
				$ratings[] = array("Price" => ($result['LowestPriceCollectibleAmount']*0.01), "Weight"=>($result['TotalCollectible']*0.9));

				if (count($result['eBayResults']) > 0) {
					foreach ($result['eBayResults'] as $key) {
						if (isset($key['Price']) && isset($this->ebayWeight[$key["Condition"]])) {
							$ratings[] = array(
								"Price" => str_replace("$", "", $key['Price']),
								"Weight"=> $this->ebayWeight[$key["Condition"]]
							);
						}
					}
				}

				$total = 0;
				$count = 0;
				foreach($ratings as $key) {
					$total += $key['Price'] * $key['Weight'];
					$count += $key['Weight'];
				}
				$result["WeightedPrice"] = round($total / $count,2);
				$this->redis->set($ASIN, json_encode($result));
				// have it expire in 15 minutes
				$this->redis->expire($ASIN, 900);
			} catch (Exception $e) {
				$result = $e;
			}
		}
		return $result;
	}
}
