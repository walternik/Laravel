<?php

namespace App\Models;

class FemalePlayer extends Player
{
    private $reaction;

    public function __construct() {
        $this->reaction = 0;
	}

    public function getReaction() {
        return $this->reaction;
    }

    public function setReaction($reaction) {
        $this->reaction = $reaction;
    }
}