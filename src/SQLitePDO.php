<?php
/*
 * Copyright (c) 2023. Manuel Daniel Dahmen
 *
 *
 *    Copyright 2012-2023 Manuel Daniel Dahmen
 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */

class SQLitePDO extends PDO
{
    private $sem;

    function __construct($filename)
    {
        $filename = realpath($filename);
        parent::__construct('sqlite:' . $filename);

        $key = ftok($filename, 'a');

        error_log("Fichier database ouvert: ".$filename);
    }

    function query($statement, $mode = PDO::ATTR_DEFAULT_FETCH_MODE, ...$fetch_mode_args)
    {
        parent::query($statement, $mode, $fetch_mode_args); // TODO: Change the autogenerated stub
    }

    function beginTransaction()
    {
        sem_acquire($this->sem);
        return parent::beginTransaction();
    }

    function commit()
    {
        $success = parent::commit();
        return $success;
    }

    function rollBack()
    {
        $success = parent::rollBack();
        return $success;
    }

}