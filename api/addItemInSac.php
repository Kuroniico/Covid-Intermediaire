<?php
session_start();
include "../fonction.php"; 
$reponse[0]=0;
$reponse[1]=0;
if($access){

    if(isset($_GET["idItem"])){

        //on doit toujours vérifier en bdd la posibilité de l'appel de API
        //iici on va pour un personnage prendre un item de la map et la mettre dans son sac.
       
        $reponse[0]=0;
        $reponse[1]=0;
        $Perso = $Joueur1->getPersonnage();
        if($Perso->getVie()==0){
            $Perso->resurection();
            $reponse[1]="ton perso est mort";
        }
        $map=$Perso->getMap();
        //une fois que j'ai mes objet je vérifie que le perso est bien sur la map
        
        $idmap = $Perso->getMap()->getId();
       
           //que l'item est bien dans la map
        foreach ($map->getItems()  as $item) {
            if($_GET["idItem"]==$item->getId()){

                //on retire l'item de la map et on la rajoute dans le sac
                $map->removeItemById($_GET["idItem"]);
                $item = new Item($mabase);
                $item->setItemByID($_GET["idItem"]);
                $Perso->addItem($item);
                $reponse[1]=1;
                $reponse[0]=1;
                
            }
        }  
        
    }
}
echo json_encode($reponse);

?>


