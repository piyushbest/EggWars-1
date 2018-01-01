<?php

declare(strict_types=1);

namespace eggwars;

use eggwars\arena\Arena;
use eggwars\level\EggWarsLevel;
use eggwars\utils\ConfigManager;
use pocketmine\level\Level;
use pocketmine\utils\Config;

/**
 * Class LevelManager
 * @package eggwars
 */
class LevelManager extends ConfigManager {

    /**
     * @var EggWarsLevel[] $levels
     */
    private $levels = [];

    private $defaultLevels = true;

    /**
     * LevelManager constructor.
     */
    public function __construct() {
        $this->loadLevels();
    }


    /**
     * @param Arena $arena
     * @return array|bool $levels
     */
    public function getLevelsForArena(Arena $arena) {
        $levels = [];
        foreach($this->levels as $level) {
            if(in_array($arena->getName(), (array)$level->data["arenas"])) {
                array_push($levels, $level);
            }
        }
        check:
        if(count($levels) < 3) {
            if(count($levels) === 0) return false;
            array_push($levels, $levels[0]);
            goto check;
        }
        return $levels;
    }

    public function loadLevels() {
        if($this->defaultLevels) {
            $this->levels["ew-test"] = new EggWarsLevel($this->defaultLevelData);
            $this->levels["EW_1"] = new EggWarsLevel($this->defaultLevelData);
            $this->levels["VixikEW"] = new EggWarsLevel($this->defaultLevelData);
        }

        foreach (glob($this->getDataFolder()."levels/*.yml") as $file) {
            $this->levels[basename($file, ".yml")] = EggWarsLevel::loadFromConfig(new Config($file, Config::YAML));
        }
    }

    /**
     * @param $name
     * @return EggWarsLevel $name
     */
    public function getLevelByName($name): EggWarsLevel {
        return $this->levels[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function levelExists(string $name): bool {
        return isset($this->levels[$name]);
    }

    /**
     * @param Level $level
     * @param string $levelName
     * @param null $data
     */
    public function addLevel(Level $level, string $levelName, $data = null) {
        $data = is_array($data) ? $data : $this->defaultLevelData;
        $data["levelName"] = $level->getName();
        $data["name"] = $levelName;
        $this->levels[$levelName] = new EggWarsLevel($data);
    }
}