<?php

/* 
 * Copyright (C) 2017 RTG
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace RTG\JDBreakoutCore;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase 
{
    
    public $mute;
    public $save;
    
    public function onEnable() {
        
        $this->getLogger()->warning("Starting JDBreakoutCore");
        
        if (!is_dir($this->getDataFolder())) {
            mkdir ($this->getDataFolder());
            mkdir ($this->getDataFolder() . "/players");
        }
        
        $this->mute = array();
        $this->save = array();
        
    }
    
    public function onCommand(\pocketmine\command\CommandSender $sender, \pocketmine\command\Command $command, $label, array $args) {
        
       switch (strtolower($command->getName())) {
           
            case "server":
               
                if ($sender->hasPermission("jdb.essentials")) 
                {
                    
                    $sender->sendMessage(TF::GREEN . "This server is running under : " . $this->getServer()->getName() . "\n" . "API: " . $this->getServer()->getApiVersion());            
                    
                } else {
                    $sender->sendMessage(TF::RED . "You have no permission to use this command!");
                }
            
                return true;
            break;
            
            case "tpall":
                
                if ($sender->isOp()) 
                {
                    
                    foreach ($this->getServer()->getOnlinePlayers() as $p) 
                        {
                        
                        $p->teleport(new \pocketmine\math\Vector3($sender->getX(), $sender->getY(), $sender->getZ()));
                        $sender->sendMessage("We are teleporting everyone to you!");
                        $p->sendMessage("You are being teleported to : " . TF::GREEN . $sender->getName());
                        
                    }
                    
                } else {
                    $sender->sendMessage(TF::RED . "You have no permission to use this command!");
                }
                
                return true;
            break;
            
            case "killall":
                
                if ($sender->isOp()) 
                {
                    
                    foreach ($this->getServer()->getOnlinePlayers() as $p) 
                    {
                        
                        $p->sendMessage(TF::RED . "You've been killed by : " . $sender->getName());
                        $p->kill();
                        $sender->sendMessage(TF::GREEN . "Successfully killed everyone!");
                        
                    }
                    
                } else {
                    $sender->sendMessage(TF::RED . "You have no permission to use this command!");
                }
                
                return true;
            break;
            
            case "mute":
                
                if ($sender->isOp()) 
                {
                    
                    if (isset($args[0])) {
                        
                        if ($args[0] instanceof Player) 
                        {
                            
                            if (!isset($this->mute[strtolower($args[0])])) {
                                
                                $this->mute[strtolower($args[0])] = strtolower($args[0]);
                                $sender->sendMessage(TF::RED . "You've muted : " . TF::GREEN . $args[0]);
                                $args[0]->sendMessage(TF::RED . "Oh no!" . TF::GREEN . "You've been muted by an Admin, please contact an Admin for further response.");
                                
                            }
                            
                        } else {
                            $sender->sendMessage(TF::RED . "[USAGE] " . TF::GREEN . "\n" . " /mute [player]");
                        }
                           
                    } else {
                        
                    }
                        
                } else {
                    $sender->sendMessage(TF::RED . "You have no permission to use this command!");
                }
                
                return true;
            break;
           
       } 
        
    }
    
    public function onMute(\pocketmine\event\player\PlayerChatEvent $e) {
        
        if (isset($this->mute[strtolower($e->getPlayer()->getName())])) {
            
            $e->setCancelled();
            $e->getPlayer()->sendTip(TF::RED . "You've been muted!");
              
        }
        
    }
    
    public function onCommandCatch(\pocketmine\event\player\PlayerCommandPreprocessEvent $e) {
        
        $msg = $e->getMessage();
        
        $array = explode(" ", trim($msg));
        $r = intval(time());
        $time = date("m-d-Y H:i:s", $r);
        
            if ($array[0] === "/kick") 
            {
                
                $this->save[strtolower($e->getPlayer()->getName())] = "Date: $time | $array[0]";
                
                $file = new \pocketmine\utils\Config($this->getDataFolder() . "players" . strtolower($e->getPlayer()->getName()) . ".yml", \pocketmine\utils\Config::YAML, array());
                
                array_push($file, $this->save[strtolower($e->getPlayer()->getName())]);
                
                $file->set($file);
                $file->save();
                
            }
        
    }
    
    public function onDisable() {
        parent::onDisable();
    }
      
}