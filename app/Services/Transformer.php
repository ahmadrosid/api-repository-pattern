<?php

namespace App\Services;

use League\Fractal\TransformerAbstract;

abstract class Transformer extends TransformerAbstract
{
    public $type;

    public abstract function transform($model);
}
