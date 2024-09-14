<?php

require 'case.php';

class Grid{
    public $x_length;
    public $y_length;
    public $grid_table;
    public $type;  ##pour différencier le type de grille : MyGrid si elle représente la grille du joueur (avec la position de tout les bateau)
                   ##                                      YourGrid si elle représente la grille du joueur adverse (le coup tiré, touché et les bateaux coulé)

    public function __construct($x, $y, $type) {
        $this->x_length = $x;
        $this->y_length = $y;
        $this->type = $type;

        $tableau = array();
        for ($i = 0; $i < $this->x_length; $i++) {
            $tableau[$i] = array();
            for ($j = 0; $j < $this->y_length; $j++) {
                if ($this->type == "MyGrid") {
                    $tableau[$i][$j] = new CaseGrille($i,$j,"water");
                }
                else {
                    $tableau[$i][$j] = new CaseGrille($i,$j,"hidden");
                }
            }

        $this->grid_table = $tableau;
        }
            
    }

    public function show_grid(){
        for ($i = 0; $i < $this->x_length; $i++) {
            for ($j = 0; $j < $this->x_length; $j++) {
                $this->grid_table[$i][$j]->show_case();
            }
            echo "\n";
        }
    }
}


$test = new Grid(10,10,"MyGrid");
$test->show_grid();

?>
