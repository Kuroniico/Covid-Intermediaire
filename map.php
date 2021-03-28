<?php 
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="main.js"></script>
    <title>Combat</title>
</head>
<body>
    <div class="centragePrincipal">
    <?php
    include "fonction.php"; 
    $access = $Joueur1->deconnectToi();
    if($access){
        
        //gestion accès map:
             
            $Personnage = $Joueur1->getPersonnage();
            echo "<div><h1>BIENVENUE " .$Joueur1->getPrenom()."</h1>";
           

            echo "<p><h3>Tu est en train de te ballader avec ". $Personnage->getNom()."</h3></p></div>";
            $Personnage->getBardeVie();
            echo "<div><p><h4>il vaut : ".$Personnage->getValeur()." NFT</h4></p></div>";
            
            $map = $Personnage->getMap();
            
            if(isset($_GET["position"]) && $Personnage->getVie()>0){
                $map = $map->loadMap($_GET["position"],$_GET["cardinalite"],$Joueur1);
            }else{
                if($Personnage->getVie()==0){
                    $Personnage->resurection();
                    $map = $Personnage->getMap();
                }
                $map = $map->loadMap($map->getPosition(),'nord',$Joueur1);
            }
           
           
            //affichage des autres joueurs sur la carte

            $listPersos = $map->getAllPersonnages();
            if(count($listPersos)>1){
                echo "<p>Visiblement tu n'est pas seul ici il y a aussi :".'<ul id="ulPersos" class="Persos">';
                foreach ( $listPersos as  $Perso) {
                    if($Perso->getId()!=$Joueur1->getPersonnage()->getId()){
                        ?>
                        <li id="Perso<?php echo $Perso->getId()?>">
                        <a onclick="AttaquerPerso(<?php echo $Perso->getId()?>,0)">
                            <?php  $Perso->renderHTML();?></a>
                        </li>
                        <?php 
                    }
                }
                echo '</ul></p>';
            }

            //affiche les mob;
            $listMob = $map->getAllMobs();
            if(count($listMob)>0){
                echo "<p>Attentions il y a :".'<ul id="ulMob" class="Persos">';
                foreach ( $listMob as  $Mob) {
                    
                        ?>
                        <li id="Mob<?php echo $Mob->getId()?>">
                        <a onclick="AttaquerPerso(<?php echo $Mob->getId()?>,1)">
                            <?php  
                            echo $Mob->generateImage();
                            $Mob->renderHTML();
                            ?>
                            
                        </a>
                        </li>
                        <?php 
                    
                }
                echo '</ul></p>';
            }
           

            //AFFICHAGE DES ITEMS DE LA MAP
            $listItems = $map->getItems();
            if(count($listItems)>0){
                echo '<p>Items Présent : <ul class="Item">';
                foreach ( $listItems as  $Item) {
                    ?>
                    <li id="item<?php echo $Item->getId()?>"><a onclick="CallApiAddItemInSac(<?php echo $Item->getId()?>)"><?php echo $Item->getNom() ?></li>
                    <?php 
                }
                echo '</ul></p>';
            }
            
            $map->getMapAdjacenteLienHTML();
            $map->getImageCssBack();


            //AFFICHAGE DES ITEMS DU SAC
            echo "<p>Voici le contenu de la bedasse de ".$Joueur1->getNomPersonnage()." </p>";
            $listItems = $Joueur1->getPersonnage()->getItems();
            echo '<p><ul id="Sac" class="Sac">';
            if(count($listItems)>0){
                foreach ( $listItems as  $Item) {
                    ?>
                    <li id="itemSac<?php echo $Item->getId()?>"><a onclick="useItem(<?php echo $Item->getId()?>)"><?php echo $Item->getNom() ?></li>
                    <?php 
                }
            }?>

            </ul></p>
            <div class="basdepage">
            <p><a  href="index.php" >retour menu choix personnage </a></p>
        </div>
            <?php
    }else{
        echo $errorMessage;
    }
    ?>
    </div><!--fin centragePrincipal"-->
</body>
<script>
function CallApiAddItemInSac(idItem){
    fetch('api/addItemInSac.php?idItem='+idItem).then((resp) => resp.json()) .then(function(data) {
    // data est la réponse http de notre API.
    console.log(data); 
    if(data[0]!=0 && data[1]==1){
        var li = document.getElementById("item"+idItem)
        var liSac = li;
        if (li!='undefine'){
            li.remove();
        }
        var ul = document.getElementById("Sac")
        if (ul!='undefine'){
            ul.appendChild(liSac);
        }
    } else{
        
        alert("vous avez pas réussi à le piquer "+data[1]);
    }  

    }) .catch(function(error) {
    // This is where you run code if the server returns any errors
    console.log(error); });
}

function DetruireItem(idItem){
    alert("bientot tu pourras en faire un truc de cet item si les dev se bouge !");
}
function AttaquerPerso(idPerso,type){
    getVie(idPerso,type)
}
</script>
</html>