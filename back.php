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

    public function set_state($state) {
        if (in_array($state, ["hidden", "boat", "water", "damaged_boat", "sinked_boat"])) {
            $this->state = $state;
        }
        else{
            echo "le type de state attribué n'est pas pris en charge";
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

    public function is_occupied($x, $y) {   ## test si la case est occupée par un objet (bateau , bateau endomagé ou bateau coulé)
        $case = $this->grid_table[$x][$y];
        if (in_array($case->get_state(), ["boat","damaged_boat", "sinked_boat"])){
            return true;
        }
    }

    public function get_x_length(){
        return $this->x_length;
    }

    public function get_y_length(){
        return $this->y_length;
    }

}

class Boat{
    public $longueur;
    public $hp;
    public $grid;
    public $coord_list = [];  ##coordonnée de chaque case composant le bateau

    public function __construct($longueur,$grid) {
        $this->hp = $longueur;
        $this->longueur = $longueur;
        $this->grid = $grid;

    }

    // Fonction pour placer le bateau sur la grille
    public function place_boat() {
        $longueur = $this->longueur;
        $success = false;

        while (!$success) {
            try {
                // Coordonnées aléatoires pour le point de départ
                $x_rand = rand(0, $this->grid->x_length - 1);
                $y_rand = rand(0, $this->grid->y_length - 1);

                // Choisir une orientation (0 = horizontal, 1 = vertical)
                $orientation = rand(0, 1);

                if ($orientation == 0) { // Placement horizontal
                    if ($x_rand + $longueur > $this->grid->x_length) {
                        throw new Exception('Le bateau dépasse les limites horizontalement.');
                    }
                    // Vérifie si toutes les cases sont libres
                    for ($i = 0; $i < $longueur; $i++) {
                        if ($this->grid->is_occupied($x_rand + $i, $y_rand)) {
                            throw new Exception('Une case est déjà occupée horizontalement.');
                        }
                    }
                    // Placement du bateau si toutes les cases sont valides
                    for ($i = 0; $i < $longueur; $i++) {
                        $case = $this->grid->grid_table[$x_rand + $i][$y_rand];
                        $case->set_state("boat");
                        $this->coord_list[] = [$x_rand + $i,$y_rand];
                    }
                } else { // Placement vertical
                    if ($y_rand + $longueur > $this->grid->y_length) {
                        throw new Exception('Le bateau dépasse les limites verticalement.');
                    }
                    // Vérifie si toutes les cases sont libres
                    for ($i = 0; $i < $longueur; $i++) {
                        if ($this->grid->is_occupied($x_rand, $y_rand + $i)) {
                            throw new Exception('Une case est déjà occupée verticalement.');
                        }
                    }
                    // Placement du bateau si toutes les cases sont valides
                    for ($i = 0; $i < $longueur; $i++) {
                        $case = $this->grid->grid_table[$x_rand][$y_rand + $i];
                        $case->set_state("boat");
                        $this->coord_list[] = [$x_rand,$y_rand + $i];
                    }
                }
                $success = true; // Si aucun problème, on sort de la boucle

            } catch (Exception $e) {
                continue;
            }
        }
    }

    public function is_alive(){
        if ($this->hp > 0){
            return true;
        }
    }

    public function get_coordinate(){
        return $this->coord_list;
    }

    public function get_real_hp(){
        $hp_counter = 0;
        for ($i = 0; $i < count($this->coord_list); $i++) {
            $x = $this->coord_list[$i][0];
            $y = $this->coord_list[$i][1];
            if ($this->grid->grid_table[$x][$y]->get_state() == "boat"){
                $hp_counter += 1;
            }
        }
        echo "le bateau a $hp_counter hp \n";
        $this->hp = $hp_counter;
        return $hp_counter;
    }
}
    class Player{
        public $boat_inventory = [];
        public $mygrid;
        public $hidden_grid;
        public $yourgrid;
    
        public function __construct($inventory,$mygrid, $yourgrid) {
            $this->boat_inventory = $inventory;
            $this->mygrid = $mygrid;
            $this->yourgrid = $yourgrid;
        }

        public function can_play(){
            $alive_boat_counter = 0;
            for ($i = 0; $i < count($this->boat_inventory); $i++){
                if ($this->boat_inventory[i]->is_alive()){
                    $alive_boat_counter += 1;
                } 
            }
            if ($alive_boat_counter != 0){
                return true;
            }
            else{
                return false;
            }
        }

        private function canon_ball($grid){  ##permet au joueur de tirer un coup de canon sur le jeu de l'adversaire
            $x_coordinate = false;
            $y_coordinate = false;
            while (!x_coordinate){
                echo "rentrez la coordonnée x de la case : \n";
                // Utilise fgets pour capturer l'input de l'utilisateur depuis la console
                $x = fgets(STDIN);
                if (0 > $x && $x > $this->grid->get_x_length()){
                    $x_coordinate = true;
                }
            }
            while (!y_coordinate){
                echo "rentrez la coordonnée x de la case : \n";
                // Utilise fgets pour capturer l'input de l'utilisateur depuis la console
                $y = fgets(STDIN);
                if (0 > $y && $y > $this->grid->get_y_length()){
                    $y_coordinate = true;
                }
            }
            $yourcase = $this->yourgrid->grid_table[$x][$y];
            $hiddencase = $this->hiddengrid_>grid_table[$x][$y];
            if ($yourcase->get_state() == "boat"){
                $yourcase->set_state("damaged_boat");
                $hiddencase->set_state("damaged_boat");
            }
            else if($yourcase->get_state() == "damaged_boat"){
                $yourcase->set_state("damaged_boat");
                $hiddencase->set_state("damaged_boat");
            }
            else if($yourcase->get_state() == "water"){
                $yourcase->set_state("water");
                $hiddencase->set_state("water");
            }
            else{
                $yourcase->set_state("sinked_boat");
                $hiddencase->set_state("sinked_boat");
            }

            
        }
     }


$test = new Grid(10,10,"MyGrid");
$test->show_grid();
echo "\n";
$boat1 = new Boat(2,$test);
$boat2 = new Boat(3,$test);
$boat3 = new Boat(3,$test);
$boat4 = new Boat(4,$test);
$boat5 = new Boat(5,$test);
$boat1->place_boat($test);
$boat2->place_boat($test);
$boat3->place_boat($test);
$boat4->place_boat($test);
$boat5->place_boat($test);
$boat1->get_real_hp();
$test->show_grid();
?>