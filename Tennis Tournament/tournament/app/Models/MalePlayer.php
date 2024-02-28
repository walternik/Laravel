<?php

namespace App\Models;

class MalePlayer extends Player
{
    private $strength;
    private $speed;

	public function __construct() {
        $this->strength = 0;
        $this->speed = 0;
	}

    public function getStrength() {
        return $this->strength;
    }

    public function setStrength($strength) {
        $this->strength = $strength;
    }

    public function getSpeed() {
        return $this->speed;
    }

    public function setSpeed($speed) {
        $this->speed = $speed;
    }
}