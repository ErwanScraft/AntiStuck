<?php

namespace AntiStuck;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\world\sound\AnvilFallSound;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        $position = $player->getPosition();
        $world = $player->getWorld();

        // Check if the player is inside a solid block on all sides
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
            // Calculate the new position 3 blocks above
            $newPosition = $position->add(0, 3, 0);

            // Get blocks in new positions
            $aboveBlock = $world->getBlock($newPosition->floor());

            // Check if the space of the 3 blocks above is empty
            if (!$aboveBlock->isSolid()) {
                $player->teleport($newPosition);
            } else {
                // If the space above is not empty, look for the next empty space to the top
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