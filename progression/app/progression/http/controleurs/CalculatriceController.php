<?php

namespace progression\http\controleurs;

class CalculatriceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    public static function calc($signe, $nb1, $nb2){
        switch ($signe){
            case "plus":
                return self::ajouter($nb1, $nb2);
                break;
            case "moins":
                return self::soustraire($nb1, $nb2);
                break;
            case "fois":
                return self::multiplier($nb1, $nb2);
                break;
            case "div":
                return self::diviser($nb1, $nb2);
                break;
            default:
                return "erreur";
        }
    }

    private static function ajouter($nb1, $nb2){
        return $nb1+$nb2;
    }
    private static function soustraire($nb1, $nb2){
        return $nb1-$nb2;
    }
    private static function multiplier($nb1, $nb2){
        return $nb1*$nb2;
    }
    private static function diviser($nb1, $nb2){
        return $nb1/$nb2;
    }
    
    
}
