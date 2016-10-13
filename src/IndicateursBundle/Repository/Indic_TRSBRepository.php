<?php

namespace IndicateursBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Indic_TRSBRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Indic_TRSBRepository extends EntityRepository
{
    /**
     * Retourne l'info trsb pour l'item
     *
     * @param $itemId
     * @return mixed
     */
    public function getTRSBByItemId($itemId)
    {
        $query = $this->createQueryBuilder('t');
        $query->select('t')
            ->where('t.Indic_items = :itemId')
            ->setParameter('itemId', $itemId);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Donne les tickets par mois et par projet en fonction de la date donnée.
     *
     * @param $year
     * @param int $month
     * @param int $project
     * @param int $requestNature
     * @param int $priority
     * @param $field
     * @return array
     */
    public function getDateByMonthProject($year,$month=-1,$project=-1,$requestNature =-1,$priority=-1,$field){
        /*
         * SELECT MONTH(t.`open_date`) mo, i.project_id p,count(t.id) FROM `indic_trsb` t left join indic_items i ON t.indic_items_id = i.id GROUP BY mo,p
         * */
        $query = $this->createQueryBuilder('t');
        $query->select('MONTH(t.'.$field.') mois, i.projectId projet,i.priority priority, i.requestNature nature,count(t.'.$field.') somme')
            ->leftJoin("t.Indic_items",'i')
            ->groupBy('mois')
            ->addGroupBy('projet')
            ->addGroupBy('nature')
            ->addGroupBy('priority')
            ->where('YEAR(t.'.$field.') = :year')
            ->orderBy('t.'.$field, 'ASC')
            ->setParameter('year', $year);

        $query = $this->projectFiltre($query,$project);
        $query = $this->monthFiltre($query,$month);
        $query = $this->natureFiltre($query,$requestNature);
        $query = $this->priorityFiltre($query,$priority);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Nombre de réouvertures sur des Tickets sur une période.
     * Réouvert ce n'est que les tickets qui ont été refusé dans leur history.
     *
     * @param $year
     * @param int $month
     * @param int $project
     * @param int $requestNature
     * @param int $priority
     * @return array
     */
    public function getRefusedCountByMonthProject($year,$month=-1,$project=-1,$requestNature =-1,$priority=-1){
        $query = $this->createQueryBuilder('t');
        $query->select('MONTH(t.correctedDate) mois, i.projectId projet, i.priority priority, i.requestNature nature,count(t.refusedCount) somme')
            ->leftJoin("t.Indic_items",'i')
            ->groupBy('mois')
            ->addGroupBy('projet')
            ->addGroupBy('nature')
            ->addGroupBy('priority')
            ->where('YEAR(t.correctedDate) = :year')
            ->andWhere('t.refusedCount IS NOT NULL')
            ->orderBy('t.correctedDate', 'ASC')
            ->setParameter('year', $year);

        $query = $this->projectFiltre($query,$project);
        $query = $this->monthFiltre($query,$month);
        $query = $this->natureFiltre($query,$requestNature);
        $query = $this->priorityFiltre($query,$priority);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Retourne le nombre d'incidentsur l'année.
     *
     * @param $year
     * @return array
     */
    public function getIncidentReouvertureCount($year){
        $query      = $this->createQueryBuilder('t');
        $query->select('count(t.refusedCount) somme');
        $query = $this->IncidentReouverture($query,$year);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Retourne les reouvertures sur incident p1.
     *
     * @param $year
     * @return array
     */
    public function getIncidentReouverture($year){
        $query      = $this->createQueryBuilder('t');
        $query->select('MONTH(t.openDate) mois, i.projectId projet,count(t.refusedCount) somme');

        $query = $this->IncidentReouverture($query,$year);

        $query->groupBy('mois')
            ->addGroupBy('projet');

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param $query
     * @param $year
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function IncidentReouverture($query,$year){
        /* @var \Doctrine\ORM\QueryBuilder $query */
        $count = 1;
        $query->leftJoin("t.Indic_items",'i')
            ->where('YEAR(t.openDate) = :year')
            ->andWhere('t.refusedCount > :count')
            ->orderBy('t.openDate', 'ASC')
            ->setParameter('year',$year)
            ->setParameter('count',$count);

        $query = $this->incidentFiltre($query);
        return $query;
    }

    /**
     * Donne le nombre de ticket support/anno consommé sur le forfait prévue.
     * Par mois et par projet
     *
     * @return mixed
     */
    public function getAllSupAnoByDateCreated(){
        $year       = '2016';
        $query      = $this->supAnoInitRequete($year);
        $query->select('count(t.openDate) total,MONTH(t.correctedDate) mois, i.projectId projet');

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Donne le nombre de ticket support/anno consommé sur le forfait prévue.
     *
     * @return mixed
     */
    public function getCountSupAnoByDateCreated(){
        $year       = '2016';
        $query      = $this->supAnoInitRequete($year);

        $query->select('count(t.openDate) total');

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Permet de selectionner les ticket du forfait (support/annomalie)
     *
     * @param $year
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function supAnoInitRequete($year){
        $bug        = 'bug';
        $support    = 'support';

        $query      = $this->createQueryBuilder('t');
        $query->leftJoin("t.Indic_items",'i')
            ->where('YEAR(t.openDate) = :year')
            ->andWhere($query->expr()->orX(
                $query->expr()->eq('i.requestNature', ':bug'),
                $query->expr()->eq('i.requestNature', ':support')
            ))
            ->setParameter('year',$year)
            ->setParameter('bug',$bug)
            ->setParameter('support',$support);

        return $query;
    }

    public function delaiTraitement($year,$project,$month,$requestNature,$priority){
        $query      = $this->createQueryBuilder('t');
        $query->select('MONTH(t.openDate) mois, i.projectId projet, i.priority priority, i.requestNature nature, i.jtracId jtracid,t.TreatmentTime delai')
            ->leftJoin("t.Indic_items",'i')
            ->where('YEAR(t.openDate) = :year')
            ->orderBy('t.openDate', 'ASC')
            ->setParameter('year', $year);

        $query = $this->projectFiltre($query,$project);
        $query = $this->monthFiltre($query,$month);
        $query = $this->natureFiltre($query,$requestNature);
        $query = $this->priorityFiltre($query,$priority);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Filtre pour selectionner les incidents
     *
     * @param \Doctrine\ORM\QueryBuilder
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function incidentFiltre($query){
        $requestNature  = 'Bug';
        $priority       = 1;

        /* @var \Doctrine\ORM\QueryBuilder $query */
        $query->andWhere('i.requestNature = :requestNature')
            ->setParameter('requestNature',$requestNature)
            ->andWhere('i.priority = :priority')
            ->setParameter('priority',$priority);

        return $query;
    }

    /**
     * Filtre sur un projet.
     *
     * @param \Doctrine\ORM\QueryBuilder
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function projectFiltre($query,$project){
        /* @var \Doctrine\ORM\QueryBuilder $query */
        if($project !=-1 && $project != 'all' && $project != Null){
            $query->andWhere('i.projectId = :project')
                ->setParameter('project',$project);
        }

        return $query;
    }
    /**
     * Filtre sur un mois.
     *
     * @param \Doctrine\ORM\QueryBuilder
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function monthFiltre($query,$month){
        /* @var \Doctrine\ORM\QueryBuilder $query */
        if($month !=-1 && $month != 'all' && $month != Null){
            $query->andWhere('MONTH(t.correctedDate) = :month')
                ->setParameter('month',$month);
        }

        return $query;
    }
    /**
     * Filtre sur une nature.
     *
     * @param \Doctrine\ORM\QueryBuilder
     * @param string $requestNature
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function natureFiltre($query,$requestNature){
        /* @var \Doctrine\ORM\QueryBuilder $query */
        if($requestNature !=-1 && $requestNature != 'all' && $requestNature != Null){
            $query->andWhere('i.requestNature LIKE :requestNature')
                ->setParameter('requestNature',$requestNature);
        }

        return $query;
    }
    /**
     * Filtre sur une priorité.
     *
     * @param \Doctrine\ORM\QueryBuilder
     * @param string $priority
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function priorityFiltre($query,$priority){
        /* @var \Doctrine\ORM\QueryBuilder $query */
        if($priority !=-1 && $priority != 'all' && $priority != Null){
            $query->andWhere('i.priority = :priority')
                ->setParameter('priority',$priority);
        }

        return $query;
    }
}
