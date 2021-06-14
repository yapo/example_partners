<?php

	/** How to use the API to delete */

	$apiDomain = 'https://m.yapo.cl';
	$apiPartner = 'api partner name';
	$apiKey = 'api partner key';
	$newAdApp = '/api/newad.json';

	$data = array('app_id' => $apiPartner);
	$url = $apiDomain . $newAdApp;
	$result = http_post_fields($url, $data);
	$jsonResponse = json_decode($result);
	$challenge = $jsonResponse->authorize->challenge;

	echo "Challenge: $challenge" . PHP_EOL;
	$hash = sha1($challenge . $apiKey);
	echo "Hash: $hash" . PHP_EOL;

	//example delete ad
 	$deleteAdApp = '/api/importdeletead.json';
	$external_ad_id = '123abc456';

	$data = array('app_id' => $apiPartner,
				  'hash' => $hash,
				  'external_ad_id' => $external_ad_id
				);


	$url = $apiDomain . $deleteAdApp;
	$result = http_post_fields($url, $data);
	// because we use latin1 and you may have errors
	$result =  utf8_encode($result);
	$jsonResponse = json_decode($result);


	if (preg_match("/TRANS_OK/",$jsonResponse->status)) {
		echo "Ad $external_ad_id delete OK." . PHP_EOL;
	} else {
		echo "Ad not deleted." . PHP_EOL;
		echo $result;
	}






function http_post_fields($url = '', $data = array(), $files = array()) {
	/* Makes a http post request to the url */

	$fields_string = http_build_query($data);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, count($data));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}


function brand_model_version_data($apiDomain, $apiPartner, $hash) {
	// Getting the cars data
	// Getting the brands first
	$carsDataApp = '/api/cars_data.json';
	$data = array('app_id' => $apiPartner, 'hash' => $hash);
	$url = $apiDomain . $carsDataApp;
	$result = http_post_fields($url, $data);
	$jsonResponse = json_decode($result);

	echo "Brands:" . PHP_EOL;
	foreach ($jsonResponse->brands as $brand) {
		echo "br_id {$brand->id}: {$brand->value}" . PHP_EOL;
	}

	// Getting the models and versions of a brand
	$carsDataApp = '/api/cars_data.json';
	$data = array('app_id' => $apiPartner, 'hash' => $hash, 'br' => 18);
	$url = $apiDomain . $carsDataApp;
	$result = http_post_fields($url, $data);
	$jsonResponse = json_decode($result);

	echo "Models and versions of brand 1:" . PHP_EOL;
	foreach ($jsonResponse->models as $model) {
		echo "mo_id {$model->key}: {$model->name}" . PHP_EOL;
		foreach ($model->versions as $version) {
			echo "version_id {$version->key}: {$version->name}" . PHP_EOL;
		}
	}
}


?>
