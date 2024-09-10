<?php

namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Entity;

class TableBehavior extends Behavior
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
    }

    public function greet($tablename): string
    {
        return ($tablename) . ' Table';
    }
}
