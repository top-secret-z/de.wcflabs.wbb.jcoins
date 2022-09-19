<?php
namespace wbb\system\jcoins\statement;
use wbb\data\board\Board;
use wbb\data\thread\Thread;
use wcf\system\jcoins\statement\DefaultJCoinsStatement;

/**
 * JCoins thread object.
 *
 * @author		2015-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wbb.jcoins
 */
class ThreadJCoinsStatement extends DefaultJCoinsStatement {
	/**
	 * @inheritdoc
	 */
	public function calculateAmount() {
		if (isset($this->parameters['amount'])) {
			return $this->parameters['amount'];
		}
		
		$object = $this->getObject();
		
		if (!empty($object) && $object instanceof Thread) {
			return $this->calculateAmountForContainer($object->getBoard());
		} else if (empty($object)) {
			return parent::calculateAmount();
		}
		
		throw new SystemException('$object is not instance of \wbb\data\thread\Thread');
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
		if (!empty($object) && $object instanceof Thread) {
			$amount = $this->calculateRetractableAmountForContainer($object->getBoard());
		}
		
		return $amount;
	}
	
	/**
	 * Calculates an amount for a container (Board)
	 */
	public function calculateAmountForContainer(Board $container) {
		if (isset($this->parameters['amount'])) {
			return $this->parameters['amount'];
		}
		
		$defaultAmount = parent::calculateAmount();
		
		if ($container->customJCoins) {
			return $container->customJCoinsAmountCreateThread;
		}
		if (JCOINS_BOARD_INHERITANCE == 'off') return $defaultAmount;
		
		// check parent boards
		while (count($container->getParentBoards())) {
			$parentBoards = $container->getParentBoards();
			if (JCOINS_BOARD_INHERITANCE == 'ascending') $container = end($parentBoards);
			if (JCOINS_BOARD_INHERITANCE == 'standard') $container = reset($parentBoards);
			
			if ($container->customJCoins) {
				return $container->customJCoinsAmountCreateThread;
			}
		}
		
		return $defaultAmount;
	}
	
	/**
	 * Calculates a retractable amount for a container (Board)
	 */
	public function calculateRetractableAmountForContainer(Board $container) {
		if (isset($this->parameters['amount'])) {
			return $this->parameters['amount'];
		}
		
		$defaultAmount = parent::calculateRetractableAmount();
		
		if ($container->customJCoins) {
			return $container->customJCoinsRetractableAmountCreateThread;
		}
		if (JCOINS_BOARD_INHERITANCE == 'off') return $defaultAmount;
		
		// check parent boards
		while (count($container->getParentBoards())) {
			$parentBoards = $container->getParentBoards();
			if (JCOINS_BOARD_INHERITANCE == 'ascending') $container = end($parentBoards);
			if (JCOINS_BOARD_INHERITANCE == 'standard') $container = reset($parentBoards);
			
			if ($container->customJCoins) {
				return $container->customJCoinsRetractableAmountCreateThread;
			}
		}
		
		return $defaultAmount;
	}
}
