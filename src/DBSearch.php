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

class DBSearch
{
    private $db;

    /***
     * @param MyDB $db
     */
    public function __construct(MyDB $db)
    {
        $this->db = $db;
    }


    /**
     * Score de recherche de mots-clés par ligne
     * @param array $assocArrayResult
     * @param array $keywords
     * @return array ["row"][idxRow] = score;
     */
    public function search(array $assocArrayResult, array $keywords): array
    {
        $result2 = array();
        foreach ($assocArrayResult as $idx => $row) {
            if(is_array($row)) {
                $score = 0;
                foreach($row as $keyColumn => $columnValue) {

                    foreach($keywords as $keyword) {
                    if ($keyword == null || strlen($keyword) == 0) {
                        if(!isset($result[$keyword])) {
                            $result[$keyword] = 0;
                        }
                        if ($result[$keyword] == 0)

                            $result[$keyword] = 0;
                        if (str_contains($keyColumn, $keyword) !== false) {
                            $result[$keyword]++;
                        }

                        if(is_string($columnValue)) {
                            if (str_contains($columnValue, $keyword) !== false) {
                                $result[$keyword]++;
                            }
                        } else if(is_numeric($columnValue)) {
                            if ($columnValue== $keyword) {
                                $result[$keyword]++;
                            }

                        }
                        $score +=$result[$keyword];
                        $total += $score;
                    }
                }
                    $result2[$idx] += $score;
                }
            }
        }

        $score = 0;

        foreach ($keywords as $keyword) {
            $score += $result[$keyword];

        }
        return array("scores by line"=>$result2, "score by keyword"=>$result, "array total score keywords"=>$score);

    }

    /**
     * @return mixed
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param mixed $db
     */
    public function setDb($db): void
    {
        $this->db = $db;
    }

    /***
     * @param array $assocOrig
     * @param array $scores
     * @return array
     */
    public function associateScore(array $assocOrig, array $scores) :  array{
        $scoresAssciated = array();
        $idx = 0;
        foreach ($assocOrig as $row) {
            $scoresAssciated[] = $assocOrig;
            $scoresAssciated["score"] = $scores["row"][$idx] + $scoresAssciated["score"];
            $idx++;
        }
        return $scoresAssciated;
    }

    /***
     * @param string $sql
     * @param array $keyworrds
     * @return array
     */
    public function searchDb(string $sql, array $keyworrds): array
    {
        $prepared = $this->db->prepare($sql);
        if($prepared->execute()===TRUE) {
            $arrayAssoc = $prepared->fetch(PDO::FETCH_ASSOC);
        }
        $res = $this->search($arrayAssoc, $keyworrds);
        return $this->associateScore($arrayAssoc, $res["scores by line"]); 
        
    }

    /***
     * @param $scoredResult
     * @param $scoreMin
     * @return array
     */
    public function filterByScore($scoredResult, $scoreMin): array
    {
        $scoresAssciated = array();
        $idx = 0;
        foreach ($scoredResult as $row) {
            if($row["score"]>=$scoreMin) {
                $scoresAssciated[] = $scoredResult;
            }
        }
        return $scoresAssciated;
    }
}
