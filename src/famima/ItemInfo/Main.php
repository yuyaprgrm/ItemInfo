<?php

namespace famima\ItemInfo;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

class Main extends PluginBase implements Listener
{
    /** @var int[] */
    private $taskId;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        foreach ($this->getServer()->getOnlinePlayers() as $player){
            $this->sendItemInfo($player);
        }
    }

    public function sendItemInfo(Player $player){
        $task = new ClosureTask(function (int $currentTick) use($player) :void{
            $itemInHand = $player->getInventory()->getItemInHand();
            $player->sendTip("Id:Damage {$itemInHand->getId()}:{$itemInHand->getDamage()}");
        });
        $this->getScheduler()->scheduleRepeatingTask($task, 20);
        $this->taskId[$player->getName()] = $task->getTaskId();
    }

    public function onItemHeld(PlayerItemHeldEvent $event){
        $itemInHand = $event->getItem();
        $event->getPlayer()->sendTip("Id:Damage {$itemInHand->getId()}:{$itemInHand->getDamage()}");
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $this->sendItemInfo($player);
    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        $this->getScheduler()->cancelTask($this->taskId[$player->getName()] ?? -1);
    }
}