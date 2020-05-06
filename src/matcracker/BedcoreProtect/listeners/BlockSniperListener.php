<?php

/*
 *     ___         __                 ___           __          __
 *    / _ )___ ___/ /______  _______ / _ \_______  / /____ ____/ /_
 *   / _  / -_) _  / __/ _ \/ __/ -_) ___/ __/ _ \/ __/ -_) __/ __/
 *  /____/\__/\_,_/\__/\___/_/  \__/_/  /_/  \___/\__/\__/\__/\__/
 *
 * Copyright (C) 2019
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author matcracker
 * @link https://www.github.com/matcracker/BedcoreProtect
 *
*/

declare(strict_types=1);

namespace matcracker\BedcoreProtect\listeners;

use BlockHorizons\BlockSniper\events\BrushUseEvent;
use matcracker\BedcoreProtect\enums\Action;
use pocketmine\block\Block;
use function array_map;
use function iterator_to_array;

final class BlockSniperListener extends BedcoreListener
{
    /**
     * @param BrushUseEvent $event
     *
     * @softDepend BlockSniper
     * @priority MONITOR
     * @ignoreCancelled
     */
    public function trackBrushUse(BrushUseEvent $event): void
    {
        $level = $event->getLevel();

        if ($this->config->isEnabledWorld($level) && $this->config->getBlockSniperHook()) {
            /** @var Block[] $newBlocks */
            $newBlocks = iterator_to_array($event->getShape()->getBlocksInside());
            /** @var Block[] $oldBlocks */
            $oldBlocks = array_map(static function (Block $block) use ($level): Block {
                return $level->getBlock($block->asVector3());
            }, $newBlocks);
            $this->blocksQueries->addBlocksLogByEntity($event->getPlayer(), $oldBlocks, $newBlocks, Action::PLACE());
        }
    }
}
