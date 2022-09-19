<?php
namespace wbb\system\jcoins\statement;
use wbb\data\post\Post;
use wbb\data\thread\Thread;
use wcf\system\exception\SystemException;
use wcf\system\jcoins\statement\DefaultJCoinsStatement;

/**
 * JCoins post object.
 *
 * @author		2015-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wbb.jcoins
 */
class PostJCoinsStatement extends DefaultJCoinsStatement {
	/**
	 * @inheritdoc
	 */
	public function calculateAmount() {
		if (isset($this->parameters['amount'])) {
			return $this->parameters['amount'];
		}
		
		$object = $this->getObject();
		
		if (!empty($object) && $object instanceof Post) {
			return $this->calculateAmountForContainer($object->getThread());
		}
		else if (empty($object)) {
			return parent::calculateAmount();
		}
		
		throw new SystemException('$object is not instance of \wbb\data\post\Post'); 
	}
	
	/**
	 * @inheritdoc
	 */
	public function calculateRetractableAmount() {
		if (isset($this->parameters['amount'])) {
			return $this->parameters['amount'];
		}
		
		$amount = 0;
		$object = $this->getObject();
		if (!empty($object) && $object instanceof Post) {
			$amount = $this->calculateRetractableAmountForContainer($object->getThread());
		}
		
		return $amount;
	}
	
	/**
	 * Calculates an amount for a container (Thread)
	 */
	public function calculateAmountForContainer(Thread $container) {
		if (isset($this->parameters['amount'])) {
			return $this->parameters['amount'];
		}
		
		$defaultAmount = parent::calculateAmount();
		
		$board = $container->getBoard();
		
		if ($board->customJCoins) {
			return $board->customJCoinsAmountCreatePost;
		}
		if (JCOINS_BOARD_INHERITANCE == 'off') return $defaultAmount;
		
		// check parent boards
		while (count($board->getParentBoards())) {
			$parentBoards = $board->getParentBoards();
			if (JCOINS_BOARD_INHERITANCE == 'ascending') $board = end($parentBoards);
			if (JCOINS_BOARD_INHERITANCE == 'standard') $board = reset($parentBoards);
			
			if ($board->customJCoins) {
				return $board->customJCoinsAmountCreatePost;
			}
		}
		
		return $defaultAmount;
	}
	
	/**
	 * Calculates a retractable amount for a container (Thread)
	 */
	public function calculateRetractableAmountForContainer(Thread $container) {
		if (isset($this->parameters['amount'])) {
			return $this->parameters['amount'];
		}
		
		$defaultAmount = parent::calculateRetractableAmount();
		
		$board = $container->getBoard();
		
		if ($board->customJCoins) {
			return $board->customJCoinsRetractableAmountCreatePost;
		}
		if (JCOINS_BOARD_INHERITANCE == 'off') return $defaultAmount;
		
		// check parent boards
		while (count($board->getParentBoards())) {
			$parentBoards = $board->getParentBoards();
			if (JCOINS_BOARD_INHERITANCE == 'ascending') $board = end($parentBoards);
			if (JCOINS_BOARD_INHERITANCE == 'standard') $board = reset($parentBoards);
			
			if ($board->customJCoins) {
				return $board->customJCoinsRetractableAmountCreatePost;
			}
		}
		
		return $defaultAmount;
	}
}
