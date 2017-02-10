<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);	// Turn on all errors, warnings and notices for easier debugging
	if (isset($_GET['search'])) {
		require_once '../class/eBayAPI.class.php';
		require_once '../class/AmazonECS.class.php';
		require_once '../class/Spyc.class.php';
		require_once '../class/Zebra_Pagination.class.php';
		$config = Spyc::YAMLLoad('../config/config.yml');

		try {
			$country = isset($_GET['country']) ? $_GET['country'] : "com";
			$category = isset($_GET['category']) ? $_GET['category'] : "All";
			$amazonEcs = new AmazonECS($config['amazon']['api-key'], $config['amazon']['api-secret'], $country, $config['amazon']['associate-tag']);
			$currentpage = isset($_GET['page']) ? $_GET['page'] : 1;
			$response = $amazonEcs->category($category)->responseGroup('Offers,Large')->optionalParameters(array('ItemPage' => $currentpage))->search($_GET['search']);
			$totalresults = $response->Items->TotalResults;
			$totalpages = $response->Items->TotalPages;
			$items = $response->Items->Item;

			foreach ($items as $key) {
				$object = json_decode(json_encode($key),1);
			// echo "<pre>";
			// print_r($object);
			// exit();
				$result = array(
					"ASIN"=>$object['ASIN'],
					"ItemManufacturer"=>$object['ItemAttributes']['Manufacturer'],
					"Title"=>$object['ItemAttributes']['Title'],
					"ImageSmall"=>(isset($object['ImageSets']['ImageSet'][0]['SmallImage']['URL'])) ? $object['ImageSets']['ImageSet'][0]['SmallImage']['URL'] : $object['ImageSets']['ImageSet']['SmallImage']['URL'],
					"ImageLarge"=>(isset($object['LargeImage']['URL'])) ? $object['LargeImage']['URL'] : $object['ImageSets']['ImageSet']['LargeImage']['URL'],
					'Features' => isset($object['EditorialReviews']['EditorialReview']['Content']) ? $object['EditorialReviews']['EditorialReview']['Content'] : "",
					"Artist"=>"",
					"Brand"=>isset($object['ItemAttributes']['Brand']) ? $object['ItemAttributes']['Brand'] : "",
					"Category"=>$object['ItemAttributes']['ProductGroup'],
					"Binding"=>$object['ItemAttributes']['Binding'],	
				);
				if (isset($object['ItemAttributes']['Artist'])){
					if (is_array($object['ItemAttributes']['Artist'])) {
						$result['Artist'] = $object['ItemAttributes']['Artist'][0];
					} else {
						$result['Artist'] = $object['ItemAttributes']['Artist'];
					}
				}
				$results[$object['ASIN']] = $result;
			}
			if (count($results) >0) {
				foreach ($results as $key) {
					$str = "<div class='panel'><div class='row'>";
					$str .= (!empty($key['ImageLarge'])) ? "<div class=\"large-2 columns\"><a href='item.php?AmazonASIN=".$key['ASIN']."'><img src=".$key['ImageLarge']."></a></div>" : "<div class=\"large-2 columns\"></div>";
					$str .= (!empty($key['Title'])) ? "<div class=\"large-10 columns\"><h4><a href='item.php?AmazonASIN=".$key['ASIN']."'>".$key['Title']."</a></h4>" : "<div class=\"large-9 columns\">";
					$str .= '<h5>'.$key['Artist'].'</h5>
					'.(!empty($key['Brand']) ? '<h5><small>Brand: </small>'.$key['Brand'].'</h5>' : "").'
					'.(!empty($key['ItemManufacturer']) ? '<h5><small>Manufacturer: </small>'.$key['ItemManufacturer'].'</h5>' : "").'
					'.(!empty($key['Binding']) ? '<h5><small>Category: </small>'.$key['Category'].' > '.$key['Binding'].'</h5>' : "");
					$str .= (!empty($key['Features'])) ? "<blockquote>".substr_replace($key['Features'], "...",100)."</blockquote></div>" : "</div>";
					$str .= "</div></div>";
					echo "$str";
				}
				$records_per_page = 10;
								// instantiate the pagination object
				$pagination = new Zebra_Pagination();

				// the number of total records is the number of records in the array
				$pagination->records($totalpages);
				$pagination->padding(false);
				$pagination->base_url("search.php");

				// records per page
				$pagination->records_per_page($records_per_page);

				// here's the magick: we need to display *only* the records for the current page
				echo "<div class='pagination-centered'>";
				$pagination->render();
				echo "</div>";
			} else {
				echo "<p>There are no results.</p><p><a href='javascript:history.back()'>Go Back</a></p>";
			}
		} catch (Exception $e) {
			print_r($e);
		}
	}
?>
