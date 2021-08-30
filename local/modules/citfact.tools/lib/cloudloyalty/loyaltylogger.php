<?php


namespace Citfact\CloudLoyalty;


class LoyaltyLogger
{
    public static function log($message = '', $title = '', $pathFolder = '')
    {
        try {
            if ($pathFolder) {
                $path = $_SERVER["DOCUMENT_ROOT"] . $pathFolder;
            } else {
                $path = $_SERVER["DOCUMENT_ROOT"] . "/local/var/log/LoyaltyLogger/custom";
            }
            mkdir($path, 0775, true);
            if ($title) {
                $title = ' - ' . $title;
            }
            file_put_contents($path . "/log-" . date('Y-m-d') . ".log",
                date('H:i:s') . $title . PHP_EOL, FILE_APPEND);
            file_put_contents($path . "/log-" . date('Y-m-d') . ".log", print_r($message, true) . PHP_EOL, FILE_APPEND);
        } catch (\Exception $e) {
        }
    }
}