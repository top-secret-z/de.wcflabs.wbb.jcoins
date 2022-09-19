<?php
namespace wbb\system\event\listener;
use wbb\data\post\Post;
use wbb\data\post\PostAction;
use wbb\data\post\PostList;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Add JCoins for new posts.
 *
 * @author		2015-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wbb.jcoins
 */
class JCoinsCreatePostListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_JCOINS) return;
		
		if ($eventObj instanceof PostAction) {
			$returnValues = $eventObj->getReturnValues();
			
			switch ($eventObj->getActionName()) {
				// move and merge are not considered
				
				case 'triggerPublication':
					foreach ($eventObj->getObjects() as $object) {
						$post = $object->getDecoratedObject();
						
						// skip guest post
						if (!$post->userID) continue;
						
						UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.post', $post);
					}
					break;
					
				case 'restore':
					// does not trigger publication
					foreach ($eventObj->getObjects() as $object) {
						// skip disabled posts and guest posts
						if ($object->isDisabled || !$object->userID) continue;
						
						$post = $object->getDecoratedObject();
						UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.post', $post);
						
						// check thread restore
						if (1 == $this->countPosts($post->threadID)) {
							$thread = $post->getThread();
							UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.thread', $thread);
						}
					}
					break;
					
				case 'disable':
					foreach ($eventObj->getObjects() as $object) {
						// skip trashed and already disabled posts and guest posts
						if ($object->isDisabled || $object->isDeleted || !$object->userID) continue;
						
						UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.post', $object->getDecoratedObject());
					}
					break;
					
				case 'trash':
					foreach ($eventObj->getObjects() as $object) {
						$post = $object->getDecoratedObject();
						
						// skip disabled and already trashed posts and guest posts
						if ($post->isDeleted || $post->isDisabled || !$post->userID) continue;
						
						UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.post', $post);
					}
					break;
					
				case 'copy':
					$postIDs = [];
					$params = $eventObj->getParameters();
					if (isset($params['postIDs'])) $postIDs = $params['postIDs'];
					
					if (empty($postIDs)) return;
					
					$postList = new PostList();
					$postList->setObjectIDs($postIDs);
					$postList->readObjects();
					$posts = $postList->getObjects();
					if (empty($posts)) return;
					
					foreach ($posts as $post) {
						// skip guest posts, disabled and deleted posts
						if (!$post->userID || $post->isDeleted || $post->isDisabled) continue;
						
						UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.post', $post);
					}
					break;
			}
		}
	}
	
	/**
	 * Count available posts in thread
	 */
	public function countPosts($threadID) {
		$sql = "SELECT	COUNT(*)
				FROM	wbb".WCF_N."_post
				WHERE	threadID = ? AND isDeleted = ? AND isDisabled = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$threadID, 0, 0]);
		
		$result = $statement->fetchSingleColumn();
	
		return $result;
	}
}
