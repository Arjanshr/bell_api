<?php

namespace App\Services;

class GeneralService
{


    public function getModels($path)
    {
        $path = app_path() . "/Models";
        $out = [];
        $results = scandir($path);
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
            $filename = $path . '/' . $result;
            if (is_dir($filename)) {
                $out = array_merge($out, $this->getModels($filename));
            } else {
                $out[] = substr($filename, 0, -4);
            }
        }
        foreach ($out as $o) {
            $explode = explode('\\', $o);
            $data[] = explode('/', $explode[4])[2];
        }
        return $data;
    }

    public function pluralize($quantity, $singular, $plural = null)
    {
        if ($quantity == 1 || !strlen($singular)) return $singular;
        if ($plural !== null) return $plural;
        // return $singular;
        $last_letter = strtolower(substr($singular, -1));
        switch ($last_letter) {
            case 'y':
                return substr($singular, 0, -1) . 'ies';
            case 's':
                return $singular . 'es';
            default:
                return $singular . 's';
        }
    }

    public function convertCamelCase($string)
    {
        $splits = preg_split('#([A-Z][^A-Z]*)#', $string, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $array = implode('-', $splits);
        return strtolower($array);
    }
}
