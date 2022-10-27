<?php

namespace App;

use Exception;

class Arena
{
    private array $monsters;
    private Hero $hero;

    private int $size = 10;

    public function __construct(Hero $hero, array $monsters)
    {
        $this->hero = $hero;
        $this->monsters = $monsters;
    }

    public function getDistance(Fighter $startFighter, Fighter $endFighter): float
    {
        $Xdistance = $endFighter->getX() - $startFighter->getX();
        $Ydistance = $endFighter->getY() - $startFighter->getY();
        return sqrt($Xdistance ** 2 + $Ydistance ** 2);
    }

    public function touchable(Fighter $attacker, Fighter $defenser): bool
    {
        return $this->getDistance($attacker, $defenser) <= $attacker->getRange();
    }

    /**
     * Get the value of monsters
     */
    public function getMonsters(): array
    {
        return $this->monsters;
    }

    /**
     * Set the value of monsters
     *
     */
    public function setMonsters($monsters): void
    {
        $this->monsters = $monsters;
    }

    /**
     * Get the value of hero
     */
    public function getHero(): Hero
    {
        return $this->hero;
    }

    /**
     * Set the value of hero
     */
    public function setHero($hero): void
    {
        $this->hero = $hero;
    }

    /**
     * Get the value of size
     */
    public function getSize(): int
    {
        return $this->size;
    }

    public function move(Fighter $fighter, string $direction)
    {
        $fighterY = $fighter->getY();
        $fighterX = $fighter->getX();

        match ($direction) {
            'N' => $fighterY -= 1,
            'W' => $fighterX -= 1,
            'E' => $fighterX += 1,
            'S' => $fighterY += 1
        };

        $outmap = match (true) {
            $fighterX < 0 => true,
            $fighterX >= $this->getSize() => true,
            $fighterY < 0 => true,
            $fighterY >= $this->getSize() => true,
            default => false
        };

        if ($outmap) {
            throw new Exception("Limite de la map !");
        }

        foreach ($this->getMonsters() as $monster) {
            if ($monster->getX() === $fighterX && $monster->getY() === $fighterY) {
                throw new Exception("Place occupé :-(");
            }
        }

        $fighter->setX($fighterX);
        $fighter->setY($fighterY);
    }

    public function battle(int $id)
    {
        $monsters = $this->monsters[$id];
        if ($this->touchable($this->getHero(), $monsters)) {
            $this->getHero()->fight($monsters);
        } else {
            throw new Exception("Le monstre est hors de portée !");
        }

        if ($monsters->isAlive()) {
            if ($this->touchable($monsters, $this->hero)) {
                $monsters->fight($this->hero);
            } else {
                throw new Exception("Le héros est hors de portée !");
            }
        } else {
            $this->hero->setExperience($this->hero->getExperience() + $monsters->getExperience());
        }
    }
}
