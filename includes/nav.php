<nav class="tab-bar">
	<section class="left-small">
		<a class="left-off-canvas-toggle menu-icon" href="#"><span></span></a>
	</section>

	<section class="middle tab-bar-section">
		<h1 class="title"><a href="index.php">Fetch</a></h1>
	</section>
</nav>

<!-- Off Canvas Menu -->
<aside class="left-off-canvas-menu">
	<!-- whatever you want goes here -->
	<ul class="off-canvas-list">
		<li style="text-align:center"><a href="index.php"><img src="img/favicon/apple-icon-120x120.png"></a></li>
		<li><a href="index.php">Home</a></li>
		<?php if(isset($_COOKIE['user']) && $_COOKIE['user'] !== false) { ?>
			<li><a href="account.php">My Account</a></li>
			<li><a href="logout.php">Log Out</a></li>
		<?php } else { ?>
			<li><a href="signup.php">Sign Up</a></li>
			<li><a href="login.php">Log In</a></li>
		<?php } ?>
		<li><a href="donate.php">Donate</a></li>
	</ul>
</aside>

<div class="row">
	<div class="large-12 columns" style="margin-top:10px;">
		<?php if (strpos($_SERVER['PHP_SELF'], "login.php")!==false || strpos($_SERVER['PHP_SELF'], "signup.php")!==false) { ?>
		<?php }	else { ?>
			<form method="get" action="search.php">
				<small>Search results are powered by <a href='http://www.amazon.com/' rel="nofollow" target="_blank">Amazon</a> and <a href='http://www.ebay.com/' rel="nofollow" target="_blank">eBay</a></small>
				<div class="row collapse">
					<div class="small-10 columns">
						<input type="text" placeholder="Search..." <?php if (isset($_GET['search'])) {
							echo "value='".$_GET['search']."'";
						}?> name="search" autofocus>
					</div>
					<div class="small-2 columns">
						<input type="submit" class="button postfix" value="Search" >
					</div>
				</div>
				<ul class="accordion" data-accordion style="margin-top:-8px;margin-bottom:25px">
					<li class="accordion-navigation">
						<a href="#panel1a" style="padding:5px"><small>Advanced Search Options</small></a>
						<div id="panel1a" class="content">
							<div class="row">
								<div class="large-1 columns">
									<label for="right-label" class="right inline">Country</label>	
								</div>
								<div class="large-5 columns">
									<select name="country">
										<?php
											$countries = array("USA"=>'com', "Germany"=>'de', "UK"=>'co.uk', "Canada"=>'ca', "France"=>'fr', "Japan"=>'co.jp', "Italy"=>'it', "China"=>'cn', "Spain"=>'es');
											foreach ($countries as $key => $value) {
												$checked = (isset($_GET['country']) && $_GET['country'] == $value) ? "selected=''" : "";
												echo "<option value='$value' $checked>$key</option>";
											}
										?>
									</select>
								</div>
								<div class="large-1 columns">
									<label for="right-label" class="right inline">Category</label>	
								</div>
								<div class="large-5 columns">
									<select name="category">
										<?php
											$categories = array(
												"All Departments"=>"All",
												"Amazon Instant Video"=>"UnboxVideo",
												"Appliances"=>"Appliances",
												"Apps & Games"=>"MobileApps",
												"Arts, Crafts & Sewing"=>"ArtsAndCrafts",
												"Automotive"=>"Automotive",
												"Baby"=>"Baby",
												"Beauty"=>"Beauty",
												"Books"=>"Books",
												"CDs & Vinyl"=>"Music",
												"Cell Phones & Accessories"=>"Wireless",
												"Clothing, Shoes & Jewelry"=>"Fashion",
												"Clothing, Shoes & Jewelry - Baby"=>"FashionBaby",
												"Clothing, Shoes & Jewelry - Boys"=>"FashionBoys",
												"Clothing, Shoes & Jewelry - Girls"=>"FashionGirls",
												"Clothing, Shoes & Jewelry - Men"=>"FashionMen",
												"Clothing, Shoes & Jewelry - Women"=>"FashionWomen",
												"Collectibles & Fine Arts"=>"Collectibles",
												"Computers"=>"PCHardware",
												"Digital Music"=>"MP3Downloads",
												"Electronics"=>"Electronics",
												"Gift Cards"=>"GiftCards",
												"Grocery & Gourmet Food"=>"Grocery",
												"Health & Personal Care"=>"HealthPersonalCare",
												"Home & Kitchen"=>"HomeGarden",
												"Industrial & Scientific"=>"Industrial",
												"Kindle Store"=>"KindleStore",
												"Luggage & Travel Gear"=>"Luggage",
												"Magazine Subscriptions"=>"Magazines",
												"Movies & TV"=>"Movies",
												"Musical Instruments"=>"MusicalInstruments",
												"Office Products"=>"OfficeProducts",
												"Patio, Lawn & Garden"=>"LawnAndGarden",
												"Pet Supplies"=>"PetSupplies",
												"Software"=>"Software",
												"Sports & Outdoors"=>"SportingGoods",
												"Tools & Home Improvement"=>"Tools",
												"Toys & Games"=>"Toys",
												"Video Games"=>"VideoGames",
												"Wine"=>"Wine",
											);
											foreach ($categories as $key => $value) {
												$checked = (isset($_GET['category']) && $_GET['category'] == $value) ? "selected=''" : "";
												echo "<option value='$value' $checked>$key</option>";
											}
										?>
									</select>
								</div>
							</div>
						</div>
					</li>
				</ul>
				<input type="hidden" name="page" value="1">
			</form>
		<?php } ?>
		<?php $badurlarray = array("", "/", "/index.php", "/signup.php", "/login.php"); if(!in_array($_SERVER["REQUEST_URI"],$badurlarray)){ ?>
			<div id="adsense-searchbar" style="margin: 0 auto 5px;height:100px;">
				<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
				<!-- search-bar -->
				<ins class="adsbygoogle"
					style="display:block;width:100%;height:100%;"
					data-ad-client="ca-pub-7935302944941905"
					data-ad-slot="6562185071"
					data-ad-format="auto"></ins>
				<script>
					(adsbygoogle = window.adsbygoogle || []).push({});
				</script>
			</div>
		<?php } ?>
		<?php
			if (isset($_COOKIE['error_message'])) {
				echo '
				<div data-alert class="alert-box alert radius">
					'.$_COOKIE['error_message'].'
					<a href="#" class="close">&times;</a>
				</div>';
				setcookie("error_message", "", time()-60,"/");
			}
			if (isset($_COOKIE['alert_message'])) {
				echo '
				<div data-alert class="alert-box success radius">
					'.$_COOKIE['alert_message'].'
					<a href="#" class="close">&times;</a>
				</div>';
				setcookie("alert_message", "", time()-60,"/");
			}
		?>
 	</div>
</div>
<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-69675216-1', 'auto');
	ga('send', 'pageview');

</script>