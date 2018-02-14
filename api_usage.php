<?php

	/** How to use the API */

	$apiDomain = 'http://m.yapo.cl';
	$apiPartner = 'partner_example';
	$apiKey = 'c82954ecb543135b7b7a7836cd854e0ba29a2727';
	$newAdApp = '/api/newad.json';

	$data = array('app_id' => $apiPartner);
	$url = $apiDomain . $newAdApp;
	$result = http_post_fields($url, $data);
	$jsonResponse = json_decode($result);
	$challenge = $jsonResponse->authorize->challenge;

	echo "Challenge: $challenge" . PHP_EOL;
	$hash = sha1($challenge . $apiKey);
	echo "Hash: $hash" . PHP_EOL;

	// You should offline mapping this
	// brand_model_version_data($apiDomain, $apiPartner, $hash);

	// image upload
	$images = array('./img1.jpg', './img2.jpg');
	$imagesIds = array();
	$i = 0;
	foreach ($images as $image) {
		$image_path = realpath($image);
		$img_data = file_get_contents($image);
		$b64_data = base64_encode($img_data);

		$files = array('image' => $b64_data);

		$data = array(
			"app_id" => $apiPartner,
			"hash" => $hash,
			"action" => "upload_image");

		$url = $apiDomain . $newAdApp;
		$result = http_post_fields($url, $data, $files);
		// because we use latin1 and you may have errors
		$result =  utf8_encode($result);
		$jsonResponse = json_decode($result);
		$imagesIds['image_id'.$i] = $jsonResponse->newad->image_id;
		$i++;
	}

	// So assuming brand 1 model 1 and version 1, we will insert an ad
	// Inserting an ad
	$data = array('app_id' => $apiPartner,
				  'hash' => $hash,
				  'action' => 'insert_ad',
				  'category' => '2020',
				  'type' => 's',
				  'body' => 'Este auto le tengo mucho cariño por eso lo vendo caro',
				  'subject' => 'Volkswagen Escarabajo 1981',
				  'phone' => '988665432',
				  'email' => 'yapodev@mailinator.com',
				  'name' => 'Seba Garate',
				  'region' => '14',
				  'communes' => '291',
				  'import' => '1',
				  'external_ad_id' => '123abc456',
				  'mileage' => '200000',
				  'gearbox' => '1',
				  'fuel' => '1',
				  'brand' => '89',
				  'model' => '14',
				  'version' => '2',
				  'regdate' => '2000',
				  'cartype' => '1',
				  'price' => '1000000',
				);

	$data = array_merge($data, $imagesIds);
	$url = $apiDomain . $newAdApp;
	$result = http_post_fields($url, $data);
	// because we use latin1 and you may have errors
	$result =  utf8_encode($result);
	$jsonResponse = json_decode($result);

	if ($jsonResponse->newad->status == 'TRANS_OK') {
		echo "Ad inserted OK." . PHP_EOL;
	} else {
		echo "Ad not inserted." . PHP_EOL;
		echo $result;
	}




function http_post_fields($url = '', $data = array(), $files = array()) {
	/* Makes a http post request to the url */

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	if (count($files) > 0) {
		$post = array_merge($data, $files);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	} else {
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// curl_setopt($ch, CURLOPT_VERBOSE, true);
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
	$result =  utf8_encode($result);
	$jsonResponse = json_decode($result);

	echo "Models and versions of brand 18:" . PHP_EOL;
	foreach ($jsonResponse->models as $model) {
		echo "mo_id {$model->key}: {$model->name}" . PHP_EOL;
		foreach ($model->versions as $version) {
			echo "version_id {$version->key}: {$version->name}" . PHP_EOL;
		}
	}
}

?>
