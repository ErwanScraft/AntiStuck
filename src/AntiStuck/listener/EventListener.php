<?php

namespace AntiStuck\listener;

use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\world\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\event\Listener;

class EventListener implements Listener {

  public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        $position = $player->getPosition();
        $world = $player->getWorld();
        
        $directions = [
            [0, 0, 0],
            [1, 0, 0],
            [-1, 0, 0],
            [0, 1, 0],
            [0, -1, 0],
            [0, 0, 1],
            [0, 0, -1],
        ];

        $isStuck = true;
        
        foreach ($directions as $dir) {
            $block = $world->getBlock($position->add($dir[0], $dir[1], $dir[2])->floor());
            if (!$block->isSolid()) {
                $isStuck = false;
                break;
            }
        }

        if ($isStuck) {
            $newPosition = $position->add(0, 3, 0);
            $aboveBlock = $world->getBlock($newPosition->floor());
            if (!$aboveBlock->isSolid()) {
                $player->teleport($newPosition);
            } else {
                for ($y = 1; $y <= 256; $y++) {
                    $newPosition = $position->add(0, $y, 0);
                    $aboveBlock = $world->getBlock($newPosition->floor());
                    if (!$aboveBlock->isSolid()) {
                        $player->teleport($newPosition);
                        $world->addSound($newPosition, new AnvilFallSound());
                        break;
                    }
                }
            }
        }
  }
}