<?php

namespace ErwanScraft\AntiStuck;

use pocketmine\plugin\PluginBase;
use ErwanScraft\AntiStuck\listener\EventListener;

class Main extends PluginBase {

  public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

}