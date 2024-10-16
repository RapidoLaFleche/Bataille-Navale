<?php
session_start();

class CaseGrille {
    public $state;

    public function __construct() {
        $this->state = "hidden"; // hidden, boat, water, damaged_boat, sinked_boat
    }

    public function show_case() {
        switch ($this->state) {
            case "hidden":
                return "⬛"; // Case cachée
            case "boat":
                return "🟫"; // Bateau
            case "water":
                return "🟦"; // Eau
            case "damaged_boat":
                return "🟨"; // Bateau touché
            case "sinked_boat":
                return "🟥"; // Bateau coulé
        }
    }
}

class Grid {
    public $cases = [];
    private $size;

    public function __construct($size) {
        $this->size = $size;
        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $this->cases[$i][$j] = new CaseGrille();
            }
        }
    }

    public function show_grid($isOpponent = false) {
        $output = "<pre>";
        foreach ($this->cases as $row) {
            foreach ($row as $case) {
                if ($isOpponent && $case->state === "boat") {
                    $output .= "⬛";     
                } else {
                    $output .= $case->show_case();
                }
            }
            $output .= "\n";
        }
        $output .= "</pre>";
        return $output;
    }

    public function can_place_boat($length, $x, $y, $horizontal) {
        if ($horizontal) {
            if ($y + $length > $this->size) return false; // Débordement à droite
            for ($j = 0; $j < $length; $j++) {
                if ($this->cases[$x][$y + $j]->state !== "hidden") {
                    return false; // Un bateau ne peut pas être placé ici
                }
            }
        } else {
            if ($x + $length > $this->size) return false; // Débordement en bas
            for ($i = 0; $i < $length; $i++) {
                if ($this->cases[$x + $i][$y]->state !== "hidden") {
                    return false; // Un bateau ne peut pas être placé ici
                }
            }
        }
        return true; // L'emplacement est valide
    }

    public function place_boat($x, $y, $length, $horizontal) {
        if ($horizontal) {
            for ($j = 0; $j < $length; $j++) {
                $this->cases[$x][$y + $j]->state = "boat";
            }
        } else {
            for ($i = 0; $i < $length; $i++) {
                $this->cases[$x + $i][$y]->state = "boat";
            }
        }
    }

    public function place_boats_randomly() {
        $boatSizes = [2, 3, 4, 5, 6];
        foreach ($boatSizes as $length) {
            $placed = false;
            while (!$placed) {
                $x = rand(0, $this->size - 1);
                $y = rand(0, $this->size - 1);
                $horizontal = rand(0, 1) === 0;

                if ($this->can_place_boat($length, $x, $y, $horizontal)) {
                    $this->place_boat($x, $y, $length, $horizontal);
                    $placed = true;
                }
            }
        }
    }

    public function shoot($x, $y) {
        $case = $this->cases[$x][$y];
        if ($case->state === "boat") {
            $case->state = "damaged_boat";
            return "Touché !";
        } elseif ($case->state === "hidden") {
            $case->state = "water";
            return "À l'eau.";
        } elseif ($case->state === "damaged_boat") {
            return "Déjà touché ici !";
        } elseif ($case->state === "water") {
            return "Déjà tiré ici !";
        }
        return "Erreur.";
    }
}

class Player {
    public $name;
    public $grid;

    public function __construct($name, $size) {
        $this->name = $name;
        $this->grid = new Grid($size);
    }

    public function shoot($x, $y) {
        return $this->grid->shoot($x, $y);
    }

    public function show_grid($isOpponent = false) {
        return $this->grid->show_grid($isOpponent);
    }
}

// Initialisation des joueurs
if (!isset($_SESSION['player1'])) {
    $_SESSION['player1'] = new Player("Joueur 1", 10);
    $_SESSION['player2'] = new Player("Joueur 2", 10); 
}

// Bateaux J1
if (!isset($_SESSION['player1_boats_placed'])) {
    $_SESSION['player1']->grid->place_boats_randomly(); // Placement aléatoire des bateaux pour le joueur 1
    $_SESSION['player1_boats_placed'] = true;
}

// Bateaux J2
if (!isset($_SESSION['player2_boats_placed'])) {
    $_SESSION['player2']->grid->place_boats_randomly(); // Placement aléatoire des bateaux pour le joueur 2
    $_SESSION['player2_boats_placed'] = true;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $x = intval($_POST['x']);
    $y = intval($_POST['y']);
    $currentPlayer = $_SESSION['current_player'] ?? 'player1';

    if ($currentPlayer === 'player1') {
        $message = $_SESSION['player2']->shoot($x, $y);
        $_SESSION['current_player'] = 'player2';
    } else {
        $message = $_SESSION['player1']->shoot($x, $y);
        $_SESSION['current_player'] = 'player1';
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
        <?php echo $_SESSION['player2']->show_grid(true); ?>
    </div>

    <p><?php echo $message; ?></p>

    <form method="POST">
        <input type="number" name="x" placeholder="Coordonnée X" required min="0" max="9">
        <input type="number" name="y" placeholder="Coordonnée Y" required min="0" max="9">
        <button type="submit">Tirer</button>
    </form>

</body>

</html>