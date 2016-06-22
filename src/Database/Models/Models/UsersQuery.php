<?php

namespace Models\Models;

use Models\Models\Base\UsersQuery as BaseUsersQuery;
use Propel\Runtime\ActiveQuery\Criteria;

/**
 * Skeleton subclass for performing query and update operations on the 'users' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class UsersQuery extends BaseUsersQuery
{
    public function init()
    {
        $this->tableCreate = true;
        $this->tableUpdate = true;
        $this->tableDelete = false;
        $this->tableOrder = [
            'id' => Criteria::DESC
        ];

        $this->tableColumnsDisable = [
            'email' => true
        ];
    }
}
