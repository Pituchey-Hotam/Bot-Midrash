<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class logger {
	public static function log($type, $data, $res = array()) {
		$sqldata = array(
			"type" => $type,
			"data" => json_encode($data, true),
			"res" => json_encode($res, true),
		);

		return db::insert("facebook_api_log", $sqldata);
	}

	public static function logMessage($facebookId, $chatPhoneNumber, $timestamp, $messageType, $messageContent, $messageStatus) {
		$sqldata = array(
			"id" => NULL,
			"facebook_id" => $facebookId,
			"chat_phone_number" => $chatPhoneNumber,
			"timestamp" => $timestamp,
			"message_type" => $messageType,
			"message_content" => $messageContent,
			"status" => $messageStatus
		);

		return db::insert("messages_log", $sqldata);
	}

	public static function updateMessageStatus($facebookId, $status) {
		return (new db())->where("facebook_id", $facebookId)->update("messages_log", ["status" => $status]);
	}

	public static function messageExistByFacebookId($facebookId) {
		(new db())->where("facebook_id", $facebookId)->select("messages_log", "id");
		return db::num_rows() > 0;
	}
}
