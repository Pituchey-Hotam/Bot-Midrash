<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class facebookApi {
	private static function sendApiCall($data) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v13.0/' . PHONE_ID . '/messages');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, true));

		if (DEBUG) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		}
		
		$headers = array();
		$headers[] = 'Authorization: Bearer ' . ACCESS_TOKEN;
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$result = json_decode(curl_exec($ch), true);

		if (curl_errno($ch)) {
			helpers::logErrorToFile("CURL Error -> " . curl_error($ch));
		}

		curl_close($ch);
		
		logger::log("output", $data, $result);

		if (!(isset($result['success']) && $result['success'] == true)) {
			if (!isset($result['messages']) || empty($result['messages'])) {
				helpers::logErrorToFile("Message not sent -> " . json_encode($data, true));
			}

			logger::logMessage(
				$result['messages'][0]['id'],
				$data['to'],
				time(),
				$data['type'],
				json_encode($data, true),
				avalibleWaUpdateStatuses::WAITING
			);
		}

		return $result;
	}

	public static function downloadMedia($mediaId, $filePath){
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v15.0/' . $mediaId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$headers = array();
		$headers[] = 'Authorization: Bearer ' . ACCESS_TOKEN;
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$result = json_decode(curl_exec($ch), true);
		curl_close($ch);
		
		logger::log("get_media", ["mediaId" => $mediaId, "filePath" => $filePath], $result);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $result['url']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$headers2 = array();
		$headers2[] = 'Authorization: Bearer ' . ACCESS_TOKEN;
		$headers2[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36';
		$headers2[] = 'Sec-Fetch-Mode: navigate';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers2);
		
		file_put_contents($filePath, curl_exec($ch));

		curl_close($ch);
		
		return true;
	}

	public static function markMessageRead($messageId){
		$data = array();
		$data['messaging_product'] = "whatsapp";
		$data['status'] = "read";
		$data['message_id'] = $messageId;
		
		return self::sendApiCall($data);
	}

	private static function getBaseMessageArr($to, $type, $messageId) {
		$data = array();
		$data['messaging_product'] = "whatsapp";
		$data['recipient_type'] = "individual";
		$data['type'] = $type;
		$data['to'] = $to;
		
		if(!empty($messageId)){
			$data['context']['message_id'] = $messageId;
		}

		return $data;
	}

	public static function sendLocation($to, $longitude, $latitude, $name, $address, $replyToMessageId = null) {
		$data = self::getBaseMessageArr($to, "location", $replyToMessageId);

		$data['location'] = array();
        $data['location']['longitude'] = $longitude;
        $data['location']['latitude'] = $latitude;
        $data['location']['name'] = $name;
        $data['location']['address'] = $address;

		return self::sendApiCall($data);
	}

	public static function sendInteractive($to, $content, $replyToMessageId = null) {
		$data = self::getBaseMessageArr($to, "interactive", $replyToMessageId);

		$data['interactive'] = $content;

		return self::sendApiCall($data);
	}

	public static function sendImage($to, $content, $replyToMessageId = null) {
		$data = self::getBaseMessageArr($to, "image", $replyToMessageId);

		$data['image'] = $content;

		return self::sendApiCall($data);
	}

	public static function sendContacts($to, $content, $replyToMessageId = null) {
		$data = self::getBaseMessageArr($to, "contacts", $replyToMessageId);

		$data['contacts'] = $content;

		return self::sendApiCall($data);
	}

	public static function sendTemplate($to, $content, $replyToMessageId = null) {
		$data = self::getBaseMessageArr($to, "template", $replyToMessageId);

		$data['template'] = array(
            "name" => $content['templateName'],
            "language" => $content['language']
        );
        
        if(isset($content['components'])) {
            $data['template']['components'] = $content['components'];
		}

		return self::sendApiCall($data);
	}

	public static function sendText($to, $content, $replyToMessageId = null) {
		$data = self::getBaseMessageArr($to, "text", $replyToMessageId);

		$data['text'] = array("preview_url" => true, "body" => $content);

		return self::sendApiCall($data);
	}
}
