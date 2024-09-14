<?php

require 'grid.php';
require 'case.php';

class Boat{
    public $hp;
    public $coord_list;  ##coordonnée de chaque case composant le bateau

    public function __construct($hp) {
        $this->hp = $hp;

    }

    public function place_boat($grid){               ## en cours de dev
        $x_rand = rand(0, $grid->x_length);
        $y_rand = rand(0, $grid->y_length);

    }

    public function is_alive(){
        if ($this->hp > 0){
            return TRUE;
        }
    }

    public function get_coordinate(){

    }


}
?>