<?php

namespace meta;

require_once 'AnnotationRepository.php';

class AnnotationSet
{
    public $hash = '';
    public $youtubeId = '';
    public $annotations = array();

    public function __construct($resultSet)
    {
        if (count($resultSet) > 0)
        {
            $this->hash = $resultSet[0]['hash'];
            $this->youtubeId = $resultSet[0]['youtubeid'];

            foreach ($resultSet as $rawAnnotation)
            {
                array_push($this->annotations, new \meta\Annotation($rawAnnotation));
            }

            usort($this->annotations, function($a, $b)
            {
                return $a->time - $b->time;
            });
        }
    }
}

class AnnotationSetRepository
{
    public function create($db, $youtubeid)
    {
        $hash = uniqid();

        $rawQuery = 'INSERT INTO AnnotationSet (hash, youtubeid) VALUES (:hash, :youtubeid) RETURNING hash, youtubeid';

        $query = $db->prepare($rawQuery);
        $query->execute(array(
            ':hash' => $hash,
            ':youtubeid' => $youtubeid));

        $results = $query->fetchAll(\PDO::FETCH_ASSOC);

        if ($results == false)
        {
            return new \meta\AnnotationSet();
        }

        return new \meta\AnnotationSet($results);
    }

    public function getByHash($db, $hash)
    {
        $rawQuery = 'SELECT hash, youtubeid, annotationtext, annotationtime FROM AnnotationSet AS annoset
JOIN Annotation AS anno ON annoset.id = anno.setId
WHERE hash = :hash';

        $query = $db->prepare($rawQuery);
        $query->execute(array(':hash' => $hash));

        $results = $query->fetchAll(\PDO::FETCH_ASSOC);

        if ($results == false)
        {
            return new \meta\AnnotationSet();
        }

        return new \meta\AnnotationSet($results);
    }
}

?>