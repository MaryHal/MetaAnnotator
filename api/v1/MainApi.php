<?php

namespace meta;

require_once 'Api.php';
require_once 'Database.php';
require_once 'AnnotationSetRepository.php';
require_once 'AnnotationRepository.php';

class MainApi extends Api
{
    protected $db = null;

    public function __construct($request)
    {
        parent::__construct($request);
        $this->db = \meta\Database::makeConnection();
    }

    public function annotationset()
    {
        if ($this->requestMethod == 'GET')
        {
            if (!isset($this->args['uid']))
            {
                return "No uid specified";
            }

            $repository = new \meta\AnnotationSetRepository();
            $annotationSet = $repository->getByHash($this->db, $this->args['uid']);
            return $annotationSet;
        }
        else if ($this->requestMethod == 'POST')
        {
            if (!isset($this->args['youtubeid']))
            {
                return "No youtubeid specified";
            }

            $repository = new \meta\AnnotationSetRepository();
            $annotationSet = $repository->create($this->db, $this->args['youtubeid']);
            return $annotationSet;
        }
    }

    public function annotation()
    {
        if ($this->requestMethod == 'GET')
        {
            return "Not supported yet";
        }
        else if ($this->requestMethod == 'POST')
        {
            if (!isset($this->args['uid']))
            {
                return "No uid specified";
            }

            $repository = new \meta\AnnotationRepository();
            $annotation = $repository->create($this->db, $this->args['uid'], $this->args['time'], $this->args['text']);
            return $annotation;
        }
    }
}

?>