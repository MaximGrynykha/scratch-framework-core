<?php

namespace Ismaxim\ScratchFrameworkCore;

use Ismaxim\ScratchFrameworkCore\db\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName(): string;
}