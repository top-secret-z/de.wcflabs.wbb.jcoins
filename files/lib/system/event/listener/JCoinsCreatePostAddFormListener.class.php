<?php
namespace wbb\system\event\listener;
use wbb\data\thread\ViewableThread;
use wbb\page\ThreadPage;
use wbb\system\jcoins\statement\PostJCoinsStatement;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\exception\NamedUserException;
use wcf\system\message\QuickReplyManager;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Checks whether the user has enough JCoins to answer a thread.
 *
 * @author		2015-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wbb.jcoins
 */
class JCoinsCreatePostAddFormListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_JCOINS || JCOINS_ALLOW_NEGATIVE) return;
		
		// must be user and have permission
		if (!WCF::getUser()->userID || !WCF::getSession()->getPermission('user.jcoins.canEarn') || !WCF::getSession()->getPermission('user.jcoins.canUse')) return;
		
		$statement = UserJCoinsStatementHandler::getInstance()->getStatementProcessorInstance('de.wcflabs.jcoins.statement.post');
		
		if ($statement instanceof PostJCoinsStatement) {
			if ($eventObj instanceof ThreadPage) {
				if ($statement->calculateAmountForContainer($eventObj->thread->getDecoratedObject()) < 0 && ($statement->calculateAmountForContainer($eventObj->thread->getDecoratedObject()) * -1) > WCF::getUser()->jCoinsAmount) {
					WCF::getTPL()->assign([
							'hasEnougthJCoins' => false
					]);
				}
			} 
			else if ($eventObj instanceof QuickReplyManager && $eventObj->container instanceof ViewableThread) {
				if ($statement->calculateAmountForContainer($eventObj->container->getDecoratedObject()) < 0 && ($statement->calculateAmountForContainer($eventObj->container->getDecoratedObject()) * -1) > WCF::getUser()->jCoinsAmount) {
					throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.jcoins.amount.tooLow'));
				}
			}
		}
	}
}
