<?php

namespace App\Model\Work\Entity\Projects\Task\Event;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Task\Id;

class TaskExecutorAssigned
{
    public function __construct(public Id $id, public MemberId $memberId)
    {
    }
}