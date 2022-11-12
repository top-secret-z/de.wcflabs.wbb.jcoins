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

use wbb\acp\form\BoardEditForm;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;

/**
 * JCoins board add listener.
 */
class JCoinsBoardAddListener implements IParameterizedEventListener
{
    /**
     * @inheritdoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_JCOINS) {
            return;
        }

        switch ($eventName) {
            case 'readFormParameters':
                if (isset($_POST['customJCoins'])) {
                    $eventObj->customJCoins = 1;
                }
                if (isset($_POST['customJCoinsAmountCreateThread'])) {
                    $eventObj->customJCoinsAmountCreateThread = \intval($_POST['customJCoinsAmountCreateThread']);
                }
                if (isset($_POST['customJCoinsAmountCreatePost'])) {
                    $eventObj->customJCoinsAmountCreatePost = \intval($_POST['customJCoinsAmountCreatePost']);
                }
                if (isset($_POST['customJCoinsRetractableAmountCreatePost'])) {
                    $eventObj->customJCoinsRetractableAmountCreatePost = \intval($_POST['customJCoinsRetractableAmountCreatePost']);
                }
                if (isset($_POST['customJCoinsRetractableAmountCreateThread'])) {
                    $eventObj->customJCoinsRetractableAmountCreateThread = \intval($_POST['customJCoinsRetractableAmountCreateThread']);
                }
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
                    'customJCoinsRetractableAmountCreateThread' => $eventObj->customJCoinsRetractableAmountCreateThread,
                ]);
                break;
        }
    }
}
