<?php
session_start();

class CaseGrille {
    public $state;

    public function __construct() {
        $this->state = "hidden";   //valeur par défaut
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

    public function show_case() {
        switch ($this->state) {
            case "hidden":
                return "⬛"; // case cachée valeur pas défaut
            case "boat":
                return "🟫"; // bateau
            case "water":
                return "🟦"; // eau
            case "damaged_boat":
                return "🟨"; // bateau touché
            case "sinked_boat":
                return "🟥"; // bateau coulé
        }
    }
}

class Grid {
    public $grid_table = [];
    public $size;

    public function __construct($size) {
        $this->size = $size;
        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $this->grid_table[$i][$j] = new CaseGrille();
            }
        }
    }

    public function show_grid($revealAll = false) {
        $output = "<pre>";
        foreach ($this->grid_table as $row) {
            foreach ($row as $case) {
                // affiche l'état réel uniquement si la case est "water", "damaged_boat", ou "sinked_boat"
                if ($revealAll || in_array($case->get_state(), ["water", "damaged_boat", "sinked_boat"])) {
                    $output .= $case->show_case();
                } else {
                    $output .= "⬛"; // affiche comme caché si la case n'a pas encore été touchée pour qu'aucun des joueurs ne puisse voir le jeu de l'autre
                }
            }
            $output .= "\n";
        }
        $output .= "</pre>";
        return $output;
    }
    


    public function is_occupied($x, $y) {   // test si la case est occupée par un objet (bateau , bateau endomagé ou bateau coulé)
        $case = $this->grid_table[$x][$y];
        if (in_array($case->get_state(), ["boat","damaged_boat", "sinked_boat"])){
            return true;
        }
        return false;
    }


    public function shoot($x, $y) {
        $case = $this->grid_table[$x][$y];
        if ($case->state === "boat") {
            $case->state = "damaged_boat";
            return "Touché !";
        } elseif ($case->state === "hidden") {
            $case->state = "water";
            return "À l'eau.";
        } elseif ($case->state === "damaged_boat" || $case->state === "sinked_boat") {
            return "Déjà touché ici !";
        } elseif ($case->state === "water") {
            return "Déjà tiré ici !";
        }
        return "Erreur.";
    }
}

class Boat {
        public $longueur;
        public $hp;
        public $grid;
        public $coord_list = [];
    
        public function __construct($longueur,$grid) {
            $this->hp = $longueur;
            $this->longueur = $longueur;
            $this->grid = $grid;
        }
    
        public function place_boat() {
            $longueur = $this->longueur;
            $success = false;
    
            while (!$success) {
                try {
                    $x_rand = rand(0, $this->grid->size - 1);
                    $y_rand = rand(0, $this->grid->size - 1);
                    $orientation = rand(0, 1);
    
                    if ($orientation == 0) { //horizontal
                        if ($x_rand + $longueur > $this->grid->size) {
                            throw new Exception('Le bateau dépasse les limites horizontalement.');
                        }
                        for ($i = 0; $i < $longueur; $i++) {
                            if ($this->grid->is_occupied($x_rand + $i, $y_rand)) {
                                throw new Exception('Une case est déjà occupée horizontalement.');
                            }
                        }
                        for ($i = 0; $i < $longueur; $i++) {
                            $case = $this->grid->grid_table[$x_rand + $i][$y_rand];
                            $case->set_state("boat");
                            $this->coord_list[] = [$x_rand + $i, $y_rand];
                        }
                    } else { //vertical
                        if ($y_rand + $longueur > $this->grid->size) {
                            throw new Exception('Le bateau dépasse les limites verticalement.');
                        }
                        for ($i = 0; $i < $longueur; $i++) {
                            if ($this->grid->is_occupied($x_rand, $y_rand + $i)) {
                                throw new Exception('Une case est déjà occupée verticalement.');
                            }
                        }
                        for ($i = 0; $i < $longueur; $i++) {
                            $case = $this->grid->grid_table[$x_rand][$y_rand + $i];
                            $case->set_state("boat");
                            $this->coord_list[] = [$x_rand, $y_rand + $i];
                        }
                    }
                    $success = true;
    
                } catch (Exception $e) {
                    continue;
                }
            }
        }
    
        public function is_alive(){
            if ($this->hp > 0){
                return true;
            }
            else{
                for ($i = 0; $i < count($this->coord_list); $i++) {
                    $x = $this->coord_list[$i][0];
                    $y = $this->coord_list[$i][1];
                    $this->grid->grid_table[$x][$y]->set_state("sinked_boat");
                }
                return false;
            }
        }
    
        public function get_coordinate(){
            return $this->coord_list;
        }
    
        public function calc_real_hp(){
            $hp_counter = 0;
            for ($i = 0; $i < count($this->coord_list); $i++) {
                $x = $this->coord_list[$i][0];
                $y = $this->coord_list[$i][1];
                if ($this->grid->grid_table[$x][$y]->get_state() == "boat"){
                    $hp_counter += 1;
                }
            }
            $this->hp = $hp_counter;
        }
    
    
}



class Player {
    public $name;
    public $grid;

    public $boat_inventory = [];

    public function __construct($name, $size) {
        $this->name = $name;
        $this->grid = new Grid($size);
    }

    public function shoot($x, $y) {
        return $this->grid->shoot($x, $y);
    }

    public function show_grid() {
        return $this->grid->show_grid();
    }



    public function create_boat_inventory(){
        $this->boat_inventory[0] = new Boat(2,$this->grid);
        $this->boat_inventory[1] = new Boat(2,$this->grid);
        $this->boat_inventory[2] = new Boat(3,$this->grid);
        $this->boat_inventory[3] = new Boat(3,$this->grid);
        $this->boat_inventory[4] = new Boat(5,$this->grid);
        for ($i=0; $i < 5; $i++) { 
            $this->boat_inventory[$i]->place_boat();
        }
    }

    public function can_play(){
        $alive_boat_counter = 0;
        for ($i = 0; $i < count($this->boat_inventory); $i++){
            if ($this->boat_inventory[$i]->is_alive()){
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


    public function refresh_boat_inventory(){
        for ($i=0; $i < count($this->boat_inventory); $i++) { 
            $this->boat_inventory[$i]->calc_real_hp();
            $this->boat_inventory[$i]->is_alive();
        }
    }
}

// initialisation des joueurs et de la partie
if (!isset($_SESSION['player1'])) {
    $_SESSION['player1'] = new Player("Joueur 1", 10);
    $_SESSION['player2'] = new Player("Joueur 2", 10); 
}

// bateau J1
if (!isset($_SESSION['player1_boats_placed'])) {
    $_SESSION['player1']->create_boat_inventory(); 
    $_SESSION['player1_boats_placed'] = true;
}

// bateau J2
if (!isset($_SESSION['player2_boats_placed'])) {
    $_SESSION['player2']->create_boat_inventory(); 
    $_SESSION['player2_boats_placed'] = true;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $x = intval($_POST['x']);
    $y = intval($_POST['y']);
    $currentPlayer = $_SESSION['current_player'] ?? 'player1';

    if($_SESSION['player2']->can_play() && $_SESSION['player1']->can_play()){


        if ($currentPlayer === 'player1') {
            $message = $_SESSION['player2']->shoot($x -1, $y -1);
            $_SESSION['player2']->refresh_boat_inventory();
            $_SESSION['current_player'] = 'player2';
        } else {
            $message = $_SESSION['player1']->shoot($x -1, $y -1);
            $_SESSION['player1']->refresh_boat_inventory();
            $_SESSION['current_player'] = 'player1';
        }

    }
    else{
        if($_SESSION['player2']->can_play()){
        $message = "La partie est finie , le joueur 2 a gagné";
        }
        else if($_SESSION['player1']->can_play()){
            $message = "La partie est finie , le joueur 1 a gagné";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bataille Navale</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
    }

    .grid {
        display: inline-block;
        margin: 20px;
        font-size: 24px;
    }
    </style>

</head>

<body>

    <h1>Bataille Navale</h1>

    <div class="grid">
        <h2><?php echo $_SESSION['player1']->name; ?></h2>
        <?php echo $_SESSION['player1']->show_grid(); ?>
    </div>

    <div class="grid">
        <h2><?php echo $_SESSION['player2']->name; ?></h2>
        <?php echo $_SESSION['player2']->show_grid(); ?>
    </div>

    <p><?php echo $message; ?></p>

    <form method="POST">
        <input type="number" name="x" placeholder="Coordonnée X" required min="0" max="10">
        <input type="number" name="y" placeholder="Coordonnée Y" required min="0" max="10">
        <button type="submit">Tirer</button>
    </form>

</body>

</html>