<?php

declare(strict_types=1);

class Pont
{
  // $unite ne sert que dans la classe, on met cette propriété en privé.
   private string $unite = 'm²';
  
   public float $longueur;
   public float $largeur;
  
   public function getSurface(): string
   {
       return ($this->longueur * $this->largeur) . $this->unite; // on renvoie l’unité en plus de la surface
   }
}

$towerBridge = new Pont;
$towerBridge->longueur = 286.0;
$towerBridge->largeur = 15.0;

echo $towerBridge->getSurface();


class Pont1
{
   public static function validerTaille(float $taille): bool
   {
       if ($taille < 50.0) {
           trigger_error(
               'La longueur est trop courte. (min 50m)',
               E_USER_ERROR
           );
       }
      
       return true;
   }
}

var_dump(Pont1::validerTaille(150.0));
var_dump(Pont1::validerTaille(20.0));
