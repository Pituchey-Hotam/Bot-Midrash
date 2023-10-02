<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class botMidrash{
	private $rawUpdate = array();
	private $update = NULL;

	private /* yeshivaDetails */ $yeshivaDetails;
	private /* user */ $currentUser;

	public function __construct() {
		db::connect(DATABASE_CONFIG["username"], DATABASE_CONFIG["password"], DATABASE_CONFIG["dbname"], DATABASE_CONFIG["host"]);
	}

	public function __destruct() {
		db::close();
	}

	private function loadSettings() {
		$this->currentUser = new user();
		$this->currentUser->id = (new db())->where("phone_number", $this->update->from->phoneNumber)->selectFirst('users', 'id', [])['id'] ?? -1;
		$this->currentUser->last_use = date("Y-m-d H:i:s");

		$this->yeshivaDetails = new yeshivaDetails();
		$this->yeshivaDetails->yeshivaId = $this->currentUser->yeshiva_id;

		$yeshiva = (new db())->where('id', $this->yeshivaDetails->yeshivaId)->selectFirst('yeshivot');
		if (empty($yeshiva) || $yeshiva['disable']) {
			// TODO: settings for unrgister yeshiva.

			$this->yeshivaDetails->yeshivaId = -1;
			$this->yeshivaDetails->yeshivaName = "הישיבה הכללית";
		}
		else {
			$this->yeshivaDetails->yeshivaName = $yeshiva['name'];

			$jsonSettings = array(
				'blockedNumbers',
				'shabatAvalibleOptions',
				'shabatSheetsNames',
				'shabatRemindeDates',
				'speicalRegisterData',
				'textCommands'
			);
			$settings = (new db())->where('yeshiva_id', $this->yeshivaDetails->yeshivaId)->select('settings');

			foreach ($settings as $row) {
				if (in_array($row['name'], $jsonSettings)) {
					$this->yeshivaDetails->yeshivaSettings[$row['name']] = json_decode($row['value'], true);
				}
				else {
					$this->yeshivaDetails->yeshivaSettings[$row['name']] = $row['value'];
				}
			}
		}
	}

	public function init() {
		if (helpers::isShabat()) {
			return;
		}

		$this->rawUpdate = json_decode(file_get_contents(DEBUG ? "update.json" : "php://input"), true);
		$this->update = parser::parse($this->rawUpdate);

		logger::log("input", $this->rawUpdate);

		$this->loadSettings();

		if (in_array($this->update->from->phoneNumber, $this->yeshivaDetails->yeshivaSettings['blockedNumbers'] ?? [])) {
			return;
		}
	}

	public function run() {
		if ($this->update instanceof waUpdateStatus) {
			if (logger::messageExistByFacebookId($this->update->messageId)) {
				logger::updateMessageStatus($this->update->messageId, $this->update->status);
			}
			else {
				helpers::logErrorToFile("Error update not exist -> " . $this->update->toJson());
			}
		}
		else if ($this->update instanceof waUpdateMessage) {
			if (logger::messageExistByFacebookId($this->update->messageId) && !DEBUG) {
				// TODO: what do if the message exist.
			}
			else {
				logger::logMessage(
					$this->update->messageId,
					$this->update->from->phoneNumber,
					$this->update->timestamp,
					$this->update->type,
					$this->update->toJson(),
					avalibleWaUpdateStatuses::READ
				);
				// facebookApi::markMessageRead($this->update->messageId);

				$avalibleCommands = array(
					'commandDayTimes', // V
					'commandSearchLesson', // V
					'commandSearchContact', // V
					'commandSearchContactByNumber', // V
					'commandRegisterToReminde', // V
					'commandDisbaleReminde', // V
					'commandRegisterShabat',
					'commandRegisterSpecial',
					'commandSearchInAsif',
					'commandSearchPhoto',
					'commandHelp',
					'commandContactToYeshivaAdmin',
					'commandRequestUpdatePhoto',
					'commandApprovaPhoto',
					'commandDeletePhoto',
					'commandStaticsOfShabatRegister'
				);

				$foundCommand = false;

				if (!in_array($this->currentUser->waiting_command, $avalibleCommands)) {
					$this->currentUser->waiting_command = "";
				}

				// Classes Commands
				foreach ($avalibleCommands as $className) {
					$command = (new $className());
					if ($this->update->type == $command::command_message_type) {
						if ($className == $this->currentUser->waiting_command ||
							helpers::checkIfRunThisCommand($command::command, $command::command_type, $this->update)) {
								$this->currentUser->waiting_command = "";

								if ($command::need_auth && $this->yeshivaDetails->yeshivaId == -1) {
									(new notAuthUser())->run($this->update, $this->yeshivaDetails, $this->currentUser);
									$foundCommand = true;
									break;
								}
								else {
									$command->run($this->update, $this->yeshivaDetails, $this->currentUser);
									$foundCommand = true;
									break;
								}
						}
					}
				}

				// Text Commands
				if (!$foundCommand) {
					$textCommands = (new db())->where('yeshiva_id', $this->yeshivaDetails->yeshivaId)->select('text_commands');
					foreach($textCommands as $command) {
						if (helpers::checkIfRunThisCommand(json_decode($command['command_names'], true), $command['command_type'], $this->update)) {
							if ($command['need_auth'] && $this->yeshivaDetails->yeshivaId == -1) {
								(new notAuthUser())->run($this->update, $this->yeshivaDetails, $this->currentUser);
								$foundCommand = true;
								break;
							}
							else {
								facebookApi::sendText($this->update->from->phoneNumber, $command['text'], $this->update->messageId);
								$foundCommand = true;
								break;
							}
						}
					}
				}

				if (!$foundCommand) {
					// (new defualtCommand())->run($this->update, $this->yeshivaDetails, $this->currentUser);
				}
			}
		}
		else {
			helpers::logErrorToFile("Update type not found!");
		}
	}
}
