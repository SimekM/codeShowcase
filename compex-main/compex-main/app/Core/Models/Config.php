<?php

namespace App\Core\Models;

use Nette;
use Tracy\Debugger;
use Nette\Utils\FileSystem;

final class Config
{

    // public      $language_id = "cs";    
    // public      $translate_values; 

    public function __construct
    (
        //  private Nette\Database\Explorer $database,
        // private Nette\Database\Context $context
    ) {
        // Konstruktor modelu
        // ahoj tady mates
    }


    public function getLanguages($currentDomain)
    {

        $returnArray = [];
        $pathBase = __DIR__;


        // get domains
        $pathDomains = $pathBase . '/../../config/language/domains.json';
        $fileDomains = FileSystem::read($pathDomains);
        $arrayDomains = json_decode($fileDomains);


        // get lnaguages
        $pathLanguages = $pathBase . '/../../config/language/languages.json';
        $fileLanguages = FileSystem::read($pathLanguages);
        $arrayLanguages = json_decode($fileLanguages, true);




        foreach ($arrayDomains as $domain) {
            bdump($currentDomain);
            bdump($domain->name);

            if ($currentDomain === $domain->name) {

                $returnArray['default'] = $domain->default;
                break;

            }
        }

        // sort the array so the default lang is last
        usort($arrayLanguages, function ($a, $b) use ($returnArray) {
            if ($a['key'] === $returnArray['default'])
                return 1;
            if ($b['key'] === $returnArray['default'])
                return -1;
            return 0;
        });


        $returnArray['languages'] = $arrayLanguages;

        bdump($returnArray);
        return $returnArray;

    }
}
