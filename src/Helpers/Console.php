<?php


namespace Nkaurelien\Momopay\Helpers;


class Console
{

    public static function writeLn ($mes)
    {
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        $out->writeln(is_array($mes) ? JSON::encode($mes) : $mes);
    }
}