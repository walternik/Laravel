<?php

namespace App\Models;

abstract class Player
{
    protected $name;
    protected $skill;

    public function __construct() {
        $this->name = '';
        $this->skill = 0;
	}

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getSkill() {
        return $this->skill;
    }

    public function setSkill($skill) {
        $this->skill = $skill;
    }
}
