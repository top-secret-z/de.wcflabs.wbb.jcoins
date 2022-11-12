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
namespace wbb\system\jcoins\statement;

use wbb\data\board\Board;
use wbb\data\thread\Thread;
use wcf\system\jcoins\statement\DefaultJCoinsStatement;

/**
 * JCoins thread object.
 */
class ThreadJCoinsStatement extends DefaultJCoinsStatement
{
    /**
     * @inheritdoc
     */
    public function calculateAmount()
    {
        if (isset($this->parameters['amount'])) {
            return $this->parameters['amount'];
        }

        $object = $this->getObject();

        if (!empty($object) && $object instanceof Thread) {
            return $this->calculateAmountForContainer($object->getBoard());
        } elseif (empty($object)) {
            return parent::calculateAmount();
        }

        throw new SystemException('$object is not instance of \wbb\data\thread\Thread');
    }

    /**
     * @inheritdoc
     */
    public function calculateRetractableAmount()
    {
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
    public function calculateAmountForContainer(Board $container)
    {
        if (isset($this->parameters['amount'])) {
            return $this->parameters['amount'];
        }

        $defaultAmount = parent::calculateAmount();

        if ($container->customJCoins) {
            return $container->customJCoinsAmountCreateThread;
        }
        if (JCOINS_BOARD_INHERITANCE == 'off') {
            return $defaultAmount;
        }

        // check parent boards
        while (\count($container->getParentBoards())) {
            $parentBoards = $container->getParentBoards();
            if (JCOINS_BOARD_INHERITANCE == 'ascending') {
                $container = \end($parentBoards);
            }
            if (JCOINS_BOARD_INHERITANCE == 'standard') {
                $container = \reset($parentBoards);
            }

            if ($container->customJCoins) {
                return $container->customJCoinsAmountCreateThread;
            }
        }

        return $defaultAmount;
    }

    /**
     * Calculates a retractable amount for a container (Board)
     */
    public function calculateRetractableAmountForContainer(Board $container)
    {
        if (isset($this->parameters['amount'])) {
            return $this->parameters['amount'];
        }

        $defaultAmount = parent::calculateRetractableAmount();

        if ($container->customJCoins) {
            return $container->customJCoinsRetractableAmountCreateThread;
        }
        if (JCOINS_BOARD_INHERITANCE == 'off') {
            return $defaultAmount;
        }

        // check parent boards
        while (\count($container->getParentBoards())) {
            $parentBoards = $container->getParentBoards();
            if (JCOINS_BOARD_INHERITANCE == 'ascending') {
                $container = \end($parentBoards);
            }
            if (JCOINS_BOARD_INHERITANCE == 'standard') {
                $container = \reset($parentBoards);
            }

            if ($container->customJCoins) {
                return $container->customJCoinsRetractableAmountCreateThread;
            }
        }

        return $defaultAmount;
    }
}
