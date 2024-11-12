<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=DynaPuff:wght@400..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
    <title>Classement</title>
</head>
<body>
    <?php
        require('../_header.html');
        ?>

    <main>
        <div class="zone-regles">
            <h1>Règles</h1>
            <p id="description">
                La bataille navale est un jeu de stratégie où deux joueurs s'affrontent pour essayer de couler les navires de leur adversaire.
            </p>
            <h3>Préparation :</h3>
            <p>
                Les navires de chaques joueur sont placés secretement sur une grille de 10x10 cases. Les navires occupent chacun un nombre spécifique de cases : Il y a deux navires de 2 case , deux de 3 cases et un de 5 cases.
            </p>
            <h3>Déroulement du Jeu :</h3>
            <p>
                Les joueurs jouent à tour de rôle en annonçant des coordonnées pour essayer de toucher les navires adverses. Si le tir touche un navire, la case est "touché", et la case est marquée "🟨". Si le tir rate, on dit "à l'eau" et la case est marquée "🟦". Lorsque toutes les cases d'un navire sont touchées, ce navire est déclaré "coulé" et ses cases deviennent "🟥".
            </p>
            <h3>Objectif :</h3>
            <p>
                Le but est de couler tous les navires de l'adversaire avant que les sien ne soient coulés. Le premier joueur à atteindre cet objectif gagne la partie.
            </p>


            
            
            
            
    </div>




    
    </main>
</body>
</html>