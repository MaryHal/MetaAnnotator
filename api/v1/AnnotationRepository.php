<?php

namespace meta;

class Annotation
{
    public $time = '';
    public $text = '';

    public function __construct($resultSet)
    {
        if (isset($resultSet['annotationtime']))
        {
            $this->time = $resultSet['annotationtime'];
        }

        if (isset($resultSet['annotationtext']))
        {
            $this->text = $resultSet['annotationtext'];
        }
    }
}

class AnnotationRepository
{
    public function create($db, $hash, $time, $text)
    {
        $rawQuery = 'INSERT INTO Annotation (setId, annotationtime, annotationtext)
(SELECT (SELECT id FROM AnnotationSet WHERE hash = :hash), :time, :text)
RETURNING annotationtime, annotationtext';

        $query = $db->prepare($rawQuery);
        $query->execute(array(
            ':hash' => $hash,
            ':time' => (int)$time,
            ':text' => $text));

        $results = $query->fetch(\PDO::FETCH_ASSOC);

        if ($results == false)
        {
            return new \meta\Annotation();
        }

        return new \meta\Annotation($results);
    }
}

?>
