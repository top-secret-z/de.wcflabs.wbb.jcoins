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
use wbb\data\thread\Thread;
use wbb\data\thread\ThreadAction;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;

/**
 * Add JCoins for threads.
 */
class JCoinsCreateThreadListener implements IParameterizedEventListener
{
    /**
     * @inheritdoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_JCOINS) {
            return;
        }

        if ($eventObj instanceof ThreadAction) {
            $returnValues = $eventObj->getReturnValues();

            switch ($eventObj->getActionName()) {
                // move and merge are not considered

                case 'triggerPublication':
                    foreach ($eventObj->getObjects() as $object) {
                        // skip disabled threads and guest threads
                        if (!$object->userID) {
                            continue;
                        }

                        UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.thread', $object->getDecoratedObject());
                    }
                    break;

                case 'restore':
                    // does not trigger publication, calls post action
                    foreach ($eventObj->getObjects() as $object) {
                        // skip disabled threads and guest threads
                        if ($object->isDisabled || !$object->userID) {
                            continue;
                        }

                        UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.thread', $object->getDecoratedObject());
                    }
                    break;

                case 'disable':
                    foreach ($eventObj->getObjects() as $object) {
                        // skip trashed threads and guest threads
                        if ($object->isDeleted || !$object->userID) {
                            continue;
                        }

                        UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.thread', $object->getDecoratedObject());
                    }
                    break;

                case 'trash':
                    foreach ($eventObj->getObjects() as $object) {
                        // skip disabled threads and guest threads
                        if ($object->isDisabled || !$object->userID) {
                            continue;
                        }

                        UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.thread', $object->getDecoratedObject());
                    }
                    break;

                case 'copy':
                    $returnValues = $eventObj->getReturnValues();
                    $returnValues = $returnValues['returnValues'];

                    // get new thread
                    if (\count($returnValues) != 3 || !isset($returnValues['parameters']['threadID'])) {
                        return;
                    }
                    $thread = new Thread($returnValues['parameters']['threadID']);

                    // skip guest, trashed and disabled threads
                    if (!$thread->threadID || !$thread->userID || $thread->isDeleted || $thread->isDisabled) {
                        return;
                    }

                    UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.thread', $thread);
                    break;

                case 'markAsBestAnswer':
                    // get data
                    $params = $eventObj->getParameters();
                    $thread = $eventObj->thread;

                    // revoke previous
                    if ($thread->bestAnswerPostID) {
                        $post = new Post($thread->bestAnswerPostID);
                        if ($post->postID && $post->userID) {
                            UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.bestAnswer', $thread, ['userID' => $post->userID]);
                        }
                    }

                    // award new
                    $post = new Post($params['postID']);
                    if ($post->postID && $post->userID) {
                        UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.bestAnswer', $thread, ['userID' => $post->userID]);
                    }
                    break;

                case 'unmarkAsBestAnswer':
                    // check for existing answer to revoke
                    $params = $eventObj->getParameters();
                    $thread = $eventObj->thread;

                    $post = new Post($params['postID']);
                    if ($post->postID && $post->userID) {
                        UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.bestAnswer', $thread, ['userID' => $post->userID]);
                    }
                    break;
            }
        }
    }
}
