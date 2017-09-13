<?php

namespace ProjectBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * LotRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LotRepository extends EntityRepository
{
    /**
     * Permet de selectionner des lots.
     *
     * @param $version
     * @param $project
     * @return array
     */
    public function getLots($version,$project){

        $query      = $this->createQueryBuilder('c');
        $query->select('p.name project, c.id DT_RowId, c.version, c.description, c.etat, DATE_FORMAT(c.dateCreation,\'%d/%m/%Y\') as dateCreation')
            ->leftJoin("c.project",'p')
            ->orderBy('c.dateCreation', 'ASC');


        if($version !=-1 && $version != 'version' && $version != Null){
            $query->andWhere('c.version = :version')
                ->setParameter('version',$version);
        }
        if($project !=-1 && $project != 'all' && $project != Null){
            $query->andWhere('p.projectId = :project')
                ->setParameter('project',$project);
        }

        return $query->getQuery()->getArrayResult();
    }
}