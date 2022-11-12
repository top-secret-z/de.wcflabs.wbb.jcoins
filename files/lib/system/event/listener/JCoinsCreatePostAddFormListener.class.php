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
 */
class JCoinsCreatePostAddFormListener implements IParameterizedEventListener
{
    /**
     * @inheritdoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_JCOINS || JCOINS_ALLOW_NEGATIVE) {
            return;
        }

        // must be user and have permission
        if (!WCF::getUser()->userID || !WCF::getSession()->getPermission('user.jcoins.canEarn') || !WCF::getSession()->getPermission('user.jcoins.canUse')) {
            return;
        }

        $statement = UserJCoinsStatementHandler::getInstance()->getStatementProcessorInstance('de.wcflabs.jcoins.statement.post');

        if ($statement instanceof PostJCoinsStatement) {
            if ($eventObj instanceof ThreadPage) {
                if ($statement->calculateAmountForContainer($eventObj->thread->getDecoratedObject()) < 0 && ($statement->calculateAmountForContainer($eventObj->thread->getDecoratedObject()) * -1) > WCF::getUser()->jCoinsAmount) {
                    WCF::getTPL()->assign([
                        'hasEnougthJCoins' => false,
                    ]);
                }
            } elseif ($eventObj instanceof QuickReplyManager && $eventObj->container instanceof ViewableThread) {
                if ($statement->calculateAmountForContainer($eventObj->container->getDecoratedObject()) < 0 && ($statement->calculateAmountForContainer($eventObj->container->getDecoratedObject()) * -1) > WCF::getUser()->jCoinsAmount) {
                    throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.jcoins.amount.tooLow'));
                }
            }
        }
    }
}
