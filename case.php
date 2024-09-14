<?php

class CaseGrille{
    public $x_coord;
    public $y_coord;
    public $state;

    public function __construct($x, $y,$state) {
        $this->x_coord = $x;
        $this->y_coord = $y;
        $this->state= $state;
    }

    public function set_state_($state) {
        if (in_array($state, ["hidden", "boat", "water", "damaged_boat", "sinked_boat"])) {
            $this->state = $state;
        }
    }

    public function get_state() {
        return $this->state;
    }

    public function show_case(){
        if ($this->state == "hidden"){
            echo "⬛";
        }
        elseif ($this->state == "boat"){
            echo "🟫";
        }
        elseif ($this->state == "water"){
            echo "🟦";
        }
        elseif ($this->state == "damaged_boat"){
            echo "🟨";
        }
        elseif ($this->state == "sinked_boat"){
            echo "🟥";
        }
    }
}
?>
