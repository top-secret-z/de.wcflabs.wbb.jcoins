<?php
namespace wbb\system\event\listener;
use wbb\acp\form\BoardEditForm;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;

/**
 * JCoins board add listener.
 *
 * @author		2015-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wbb.jcoins
 */
class JCoinsBoardAddListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_JCOINS) return;
		
		switch ($eventName) {
			case 'readFormParameters':
				if (isset($_POST['customJCoins'])) $eventObj->customJCoins = 1; 
				if (isset($_POST['customJCoinsAmountCreateThread'])) $eventObj->customJCoinsAmountCreateThread = intval($_POST['customJCoinsAmountCreateThread']);
				if (isset($_POST['customJCoinsAmountCreatePost'])) $eventObj->customJCoinsAmountCreatePost = intval($_POST['customJCoinsAmountCreatePost']);
				if (isset($_POST['customJCoinsRetractableAmountCreatePost'])) $eventObj->customJCoinsRetractableAmountCreatePost = intval($_POST['customJCoinsRetractableAmountCreatePost']);
				if (isset($_POST['customJCoinsRetractableAmountCreateThread'])) $eventObj->customJCoinsRetractableAmountCreateThread = intval($_POST['customJCoinsRetractableAmountCreateThread']);
				break;
				
			case 'readParameters':
				$eventObj->customJCoins = 0;
				$eventObj->customJCoinsAmountCreateThread = 0;
				$eventObj->customJCoinsAmountCreatePost = 0;
				$eventObj->customJCoinsRetractableAmountCreatePost = 0;
				$eventObj->customJCoinsRetractableAmountCreateThread = 0;
				break;
			
			case 'readData':
				if ($eventObj instanceof BoardEditForm) {
					if (empty($_POST)) {
						$eventObj->customJCoins = $eventObj->board->customJCoins;
						$eventObj->customJCoinsAmountCreateThread = $eventObj->board->customJCoinsAmountCreateThread;
						$eventObj->customJCoinsAmountCreatePost = $eventObj->board->customJCoinsAmountCreatePost;
						$eventObj->customJCoinsRetractableAmountCreatePost = $eventObj->board->customJCoinsRetractableAmountCreatePost;
						$eventObj->customJCoinsRetractableAmountCreateThread = $eventObj->board->customJCoinsRetractableAmountCreateThread;
					}
				}
				break;
				
			case 'save':
				$eventObj->additionalFields['customJCoins'] = $eventObj->customJCoins;
				$eventObj->additionalFields['customJCoinsAmountCreateThread'] = $eventObj->customJCoinsAmountCreateThread;
				$eventObj->additionalFields['customJCoinsAmountCreatePost'] = $eventObj->customJCoinsAmountCreatePost;
				$eventObj->additionalFields['customJCoinsRetractableAmountCreatePost'] = $eventObj->customJCoinsRetractableAmountCreatePost;
				$eventObj->additionalFields['customJCoinsRetractableAmountCreateThread'] = $eventObj->customJCoinsRetractableAmountCreateThread;
				break;
				
			case 'assignVariables':
				WCF::getTPL()->assign([
					'customJCoins' => $eventObj->customJCoins,
					'customJCoinsAmountCreateThread' => $eventObj->customJCoinsAmountCreateThread,
					'customJCoinsAmountCreatePost' => $eventObj->customJCoinsAmountCreatePost,
					'customJCoinsRetractableAmountCreatePost' => $eventObj->customJCoinsRetractableAmountCreatePost,
					'customJCoinsRetractableAmountCreateThread' => $eventObj->customJCoinsRetractableAmountCreateThread
				]);
				break;
		}
	}
}
