<?php

namespace App;

class Level
{
    public static function calculate(int $experience)
    {
        return ceil($experience / 1000);
    }
}
