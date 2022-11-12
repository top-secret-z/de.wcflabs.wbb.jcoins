<?php

/*
 * Copyright by Udo Zaydowicz.
 * Modified by SoftCreatR.dev.
 *
 * License: http://opensource.org/licenses/lgpl-license.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
namespace wbb\system\event\listener;

use wbb\data\post\Post;
use wbb\data\post\PostAction;
use wbb\data\post\PostList;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Add JCoins for new posts.
 */
class JCoinsCreatePostListener implements IParameterizedEventListener
{
    /**
     * @inheritdoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_JCOINS) {
            return;
        }

        if ($eventObj instanceof PostAction) {
            $returnValues = $eventObj->getReturnValues();

            switch ($eventObj->getActionName()) {
                // move and merge are not considered

                case 'triggerPublication':
                    foreach ($eventObj->getObjects() as $object) {
                        $post = $object->getDecoratedObject();

                        // skip guest post
                        if (!$post->userID) {
                            continue;
                        }

                        UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.post', $post);
                    }
                    break;

                case 'restore':
                    // does not trigger publication
                    foreach ($eventObj->getObjects() as $object) {
                        // skip disabled posts and guest posts
                        if ($object->isDisabled || !$object->userID) {
                            continue;
                        }

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
                        if ($object->isDisabled || $object->isDeleted || !$object->userID) {
                            continue;
                        }

                        UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.post', $object->getDecoratedObject());
                    }
                    break;

                case 'trash':
                    foreach ($eventObj->getObjects() as $object) {
                        $post = $object->getDecoratedObject();

                        // skip disabled and already trashed posts and guest posts
                        if ($post->isDeleted || $post->isDisabled || !$post->userID) {
                            continue;
                        }

                        UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.post', $post);
                    }
                    break;

                case 'copy':
                    $postIDs = [];
                    $params = $eventObj->getParameters();
                    if (isset($params['postIDs'])) {
                        $postIDs = $params['postIDs'];
                    }

                    if (empty($postIDs)) {
                        return;
                    }

                    $postList = new PostList();
                    $postList->setObjectIDs($postIDs);
                    $postList->readObjects();
                    $posts = $postList->getObjects();
                    if (empty($posts)) {
                        return;
                    }

                    foreach ($posts as $post) {
                        // skip guest posts, disabled and deleted posts
                        if (!$post->userID || $post->isDeleted || $post->isDisabled) {
                            continue;
                        }

                        UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.post', $post);
                    }
                    break;
            }
        }
    }

    /**
     * Count available posts in thread
     */
    public function countPosts($threadID)
    {
        $sql = "SELECT    COUNT(*)
                FROM    wbb" . WCF_N . "_post
                WHERE    threadID = ? AND isDeleted = ? AND isDisabled = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$threadID, 0, 0]);

        return $statement->fetchSingleColumn();
    }
}
