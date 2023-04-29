<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

abstract class command {
	const command = [];
	const command_message_type = 0;
	const command_type = 0;
	const need_auth = true;

	abstract public static function run(waUpdateMessage $update, yeshivaDetails $yeshivaDetails);
}
