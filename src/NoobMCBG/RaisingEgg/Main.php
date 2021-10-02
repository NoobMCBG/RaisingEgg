<?php

namespace NoobMCBG\RaisingEgg;

use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent};
use pocketmine\scheduler\ClosureTask;
use jojoe77777\FormAPI\{SimpleForm, CustomForm, ModalForm};
class Main extends PluginBase implements Listener
{
    public $money, $size, $food, $level;
    public function onEnable()
    {
        $this->getLogger()->info("Enable Plugin");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
       @mkdir($this->getDataFolder());
        $this->size = new Config($this->getDataFolder() . "size.yml", Config::YAML);
        $this->food = new Config($this->getDataFolder() . "food.yml", Config::YAML);
        $this->level = new Config($this->getDataFolder() . "level.yml", Config::YAML);
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        if (!$this->size->exists($event->getPlayer()->getName())) {
            $this->size->set($event->getPlayer()->getName(), 0);
            $this->size->save();
        }
        if (!$this->food->exists($event->getPlayer()->getName())) {
            $this->food->set($event->getPlayer()->getName(), 0);
            $this->food->save();
        }
        if (!$this->level->exists($event->getPlayer()->getName())) {
            $this->level->set($event->getPlayer()->getName(), 1);
            $this->level->save();
        }
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $a = $this->myLevelEgg($player) * 1000;
        $b = $this->myLevelEgg($player) * 100;
        $c = $this->myLevelEgg($player) * 1 - 1;
        $rand = mt_rand(1, 100);
        if ($this->myLevelEgg($player) > 1) {
            switch ($rand) {
                case 5:
                    $p->sendMessage("You Have Received " . $a . "Money From Mine Eggs");
                    $this->money->addMoney($p, $a);
                    break;
                case 20:
                    $p->sendMessage("You Have Received " . $b . "Money From Mine Eggs");
                    $this->money->addMoney($p, $a);
                    break;
                case 50:
                    $p->sendMessage("You Have Received " . $b . "Money From Mine Eggs");
                    $this->money->addMoney($p, $b);
                    break;
                default:
                    break;
            }
        }
    }

    ////End///

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()) {
            case "egg":
            if (!$s->hasPermission("eternity.command")) {
$sender->sendMessage("§cYou Not Permission To Use Command");
return true;
}else{
                $this->menu($sender);
                return true;
        }
        return true;
    }

    public function menu($sender)
    {
        $form = new SimpleForm(function (Player $sender, $data) {
            $result = $data;
            if ($result === null) {
            }
            switch ($result) {
                case 0:
                    break;
                case 1:
                    $this->TopEgg($sender);
                    break;
                case 2:
                    $this->Egg($sender);
                    break;
            }
        });
        $form->setTitle("§l§e༺§bEgg§e༻");
        $form->addButton("§l§c༺ §9Exit §c༻");
        $form->addButton("§l§c༺ §9TOP Egg §c༻");
        $form->addButton("§l§c༺ §9My Egg §c༻");
        $form->sendToPlayer($sender);
    }

    public function Egg($sender)
    {
        $form = new SimpleForm(function (Player $sender, $data) {
            $result = $data;
            if ($result === null) {
            }
            switch ($result) {
                case 0:
                    $this->menu($sender);
                    break;
                case 1:
                    //phân Bón Cao Cấp
                    $food = $this->food->get($sender->getPlayer()->getName());
                    if ($food >= 1) {
                        $exp = $this->size->get($sender->getPlayer()->getName());
                        
                        $food = $this->food->get($sender->getPlayer()->getName());
                        $sender->sendMessage("§aMulti-Strength for Successful Eggs");
                        $this->size->set($sender->getPlayer()->getName(), ($this->size->get($sender->getPlayer()->getName()) + $food*2));
                        $this->size->save();
                        $this->food->set($sender->getPlayer()->getName(), ($this->food->get($sender->getPlayer()->getName()) - $food));
                        $this->food->save();
                    }
                    if ($food == 0) {
                        $sender->sendMessage("§cYou Don't Have Enough Nutrients To Nourish Your Eggs");
                    }
                    break;
                case 2:
                    $this->levelup($sender);
                    break;
                     case 3:
                     $money = $this->money->myMoney($sender->getPlayer()->getName());
                    if ($money < 100000){
                        $sender->sendMessage("§cYou don't have enough money");
                    } else{
                         $this->food->set($sender->getPlayer()->getName(), (int)$this->food->get($sender->getPlayer()->getName()) + 5);
            $sender->sendMessage("§aSuccessfully purchase");
                        $this->money->reduceMoney($sender->getPlayer()->getName(), 100000);
            $this->food->save();
                    }
                    break;

            }
        });
        $name = $sender->getPlayer()->getName();
        $exp = $this->size->get($sender->getPlayer()->getName());
        $level = $this->level->get($sender->getPlayer()->getName());
        $food = $this->food->get($sender->getPlayer()->getName());
        $maxexp = $level * 500;
        $form->setTitle("§l§3༺ §2My EGG§3 ༻");
        $form->setContent("§l§c•§eName: §b" . $name . "\n§l§c•§eLevel: §a" . $level . "\n§l§c•§eSize: §a" . $exp . "§6§l/20\n§l§c•§eResidual Residual §a" . $food . "");
        $form->addButton("§l§c•§c Exit §c•");
        $form->addButton("§l§c•§9 Fostering §c•");
        $form->addButton("§l§c•§9 Level UP §c•");
        $form->addButton("§l§c•§9 Buy Substance §c•");
        $form->sendToPlayer($sender);
    }

    public function levelup($sender)
    {
        $player = $sender->getName();
        $money = $this->money->myMoney($player);
        $size = $this->size->get($sender->getPlayer()->getName());
        if ($money < $this->myLevelEgg($player) * 1000000){
            $sender->sendMessage("§cYou Don't Have Enough Money To Go To The Next Level");
            $sender->sendMessage("§cThe Amount To Level Up To The Next Level Is" . $this->myLevelHeo($player) * 1000000 . "Money");
        } elseif ($size < 20){
             $sender->sendMessage("§cEggs Aren't Good Enough To Level Up To The Next Level");
         } else {
            $this->level->set($player, (int)$this->level->get($player) + 1);
            $sender->sendMessage("§6§lLevel Up Successfully, Your Egg Level " . $this->myLevelEgg($player) . "!");
            $cs = $this->myLevelEgg($player);
            $this->money->reduceMoney($player, $cs * 1000000);
            $this->level->save();
            $this->size->set($player, 0);
            $this->size->save();
        }
        }

	public function TopEgg(Player $sender){
		$levelplot = $this->level->getAll();
		$message = "";
		$message1 = "";
		if(count($levelplot) > 0){
			arsort($levelplot);
			$i = 1;
			foreach($levelplot as $name => $level){
				$message .= "§eTOP §a " . $i . "§a belong to§b" . $name . " §awith§a" . $level . " Level\n";
				$message1 .= "§eTOP §a " . $i . "§a belong to§b" . $name . " §awith§a" . $level . " Level\n";
				if($i >= 10){
					break;
				}
				++$i;
			}
		}
		
		$form = new SimpleForm(function (Player $sender, ?int $data = null){
			$result = $data;
			switch($result){
				case 0:
				$this->menu($sender);
				break;
			}
		});
		$form->setTitle("§l§3•§2 TOP EGG §3•");
		$form->setContent($message);
		$form->addButton("§l§c•§c Back §c•");
		$form->sendToPlayer($sender);
		return $form;
	}
    public function myLevelEgg($player) {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        $rebirth = $this->level->get($player);
        return $rebirth;
    }
}
