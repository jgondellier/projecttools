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
     * Retourne tous les tickets par mois de création.
     *
     * @param $year
     * @param int $month
     * @param int $project
     * @param int $requestNature
     * @param int $priority
     * @param int $notClosedCorrected
     * @return array
     */
    public function getAllByMonthCreated($year,$month=-1,$project=-1,$requestNature =-1,$priority=-1,$notClosedCorrected=-1){
        $query = $this->createQueryBuilder('t');
        $query->select('MONTH(t.openDate) mois, i.projectId projet,i.priority priority, i.requestNature nature,i.status status,i.jtracId jtracid')
            ->leftJoin("t.Indic_items",'i')
            ->where('YEAR(t.openDate) = :year')
            ->orderBy('t.openDate', 'ASC')
            ->setParameter('year', $year);

        $query = $this->notClosedCorrectedFiltre($query,$notClosedCorrected);
        $query = $this->projectFiltre($query,$project);
        $query = $this->monthFiltre($query,$month);
        $query = $this->natureFiltre($query,$requestNature);
        $query = $this->priorityFiltre($query,$priority);

        return $query->getQuery()->getArrayResult();
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
    public function getDateByMonthProject($year=-1,$month=-1,$project=-1,$requestNature =-1,$priority=-1,$field){
        $query = $this->createQueryBuilder('t');
        $query->select('YEAR(t.'.$field.') annee, MONTH(t.'.$field.') mois, i.projectId projet,i.priority priority, i.requestNature nature,count(t.'.$field.') somme')
            ->leftJoin("t.Indic_items",'i')
            ->groupBy('annee')
            ->addGroupBy('mois')
            ->addGroupBy('projet')
            ->addGroupBy('nature')
            ->addGroupBy('priority')
            ->where('t.'.$field.' IS NOT NULL')
            ->orderBy('t.'.$field, 'ASC');

        $query = $this->projectFiltre($query,$project);
        $query = $this->yearFiltre($query,$year,$field);
        $query = $this->monthFiltre($query,$month,$field);
        $query = $this->natureFiltre($query,$requestNature);
        $query = $this->priorityFiltre($query,$priority);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Nombre de réouvertures sur des Tickets sur une période.
     * Réouvert ce n'est que les tickets qui ont été refusé dans leur history.
     *
     * @param int $year
     * @param int $month
     * @param int $project
     * @param int $requestNature
     * @param int $priority
     * @param string $field le champ date sur lequel se baser
     * @return array
     */
    public function getRefusedCountByMonthProject($year=-1,$month=-1,$project=-1,$requestNature =-1,$priority=-1,$field = 'correctedDate'){
        $query = $this->createQueryBuilder('t');
        $query->select('YEAR(t.'.$field.') annee, MONTH(t.'.$field.') mois, i.projectId projet, i.priority priority, i.requestNature nature,count(t.refusedCount) somme')
            ->leftJoin("t.Indic_items",'i')
            ->groupBy('annee')
            ->addGroupBy('mois')
            ->addGroupBy('projet')
            ->addGroupBy('nature')
            ->addGroupBy('priority')
            ->where('t.refusedCount IS NOT NULL')
            ->andWhere('t.'.$field.' IS NOT NULL')
            ->orderBy('t.'.$field, 'ASC');

        $query = $this->projectFiltre($query,$project);
        $query = $this->yearFiltre($query,$year,$field);
        $query = $this->monthFiltre($query,$month,$field);
        $query = $this->natureFiltre($query,$requestNature);
        $query = $this->priorityFiltre($query,$priority);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Retourne la liste des tickets fermé avec leurs délai de traitement.
     *
     * @param $year
     * @param $project
     * @param $month
     * @param $requestNature
     * @param $priority
     * @param string $field
     * @return array
     */
    public function delaiTraitement($year,$project,$month,$requestNature,$priority,$field='openDate'){
        $query      = $this->createQueryBuilder('t');
        $query->select('YEAR(t.'.$field.') annee, MONTH(t.'.$field.') mois, i.projectId projet, i.priority priority, i.requestNature nature, i.jtracId jtracid,t.TreatmentTime delai')
            ->leftJoin("t.Indic_items",'i')
            ->where('t.'.$field.' IS NOT NULL')
            ->andWhere('t.correctedDate IS NOT NULL')
            ->orderBy('t.'.$field, 'ASC');

        $query = $this->projectFiltre($query,$project);
        $query = $this->yearFiltre($query,$year,$field);
        $query = $this->monthFiltre($query,$month,$field);
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
     * @param $year
     * @return mixed
     */
    public function getCountSupAnoByDateCreated($year){
        if ($year){
            $query      = $this->supAnoInitRequete($year);

            $query->select('count(t.openDate) total');

            return $query->getQuery()->getOneOrNullResult();
        }
        return Null;
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

    public function delaiTraitementIncidentContractuel($year,$project=-1,$month=-1){
        $requestNature  = "bug";
        $priority       = 'p1';
        $timeMax        = '240';

        $query          = $this->createQueryBuilder('t');
        $query->select('count(t.id) nb')
            ->addSelect('(SELECT count(tr.id) 
                        FROM IndicateursBundle\Entity\Indic_TRSB tr 
                        LEFT JOIN IndicateursBundle\Entity\Indic_items it WITH tr.Indic_items = it.id 
                        WHERE YEAR(tr.openDate) = :year and it.requestNature LIKE :requestNature and it.priority = :priority) total')
            ->leftJoin("t.Indic_items",'i')
            ->where('YEAR(t.openDate) = :year')
            ->andWhere('t.TreatmentTime < :timeMax')
            ->orderBy('t.openDate', 'ASC')
            ->setParameter('timeMax', $timeMax)
            ->setParameter('priority', $priority)
            ->setParameter('requestNature', $requestNature)
            ->setParameter('year', $year);

        $query = $this->projectFiltre($query,$project);
        $query = $this->monthFiltre($query,$month);
        $query = $this->natureFiltre($query,$requestNature);
        $query = $this->priorityFiltre($query,$priority);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Delai de traitement des tickets support
     *
     * @param $year
     * @param int $project
     * @param int $month
     * @return mixed
     */
    public function delaiTtmntSupport($year,$project=-1,$month=-1){
        $requestNature  = "support";

        $query          = $this->createQueryBuilder('t');
        $query->select('count(t.id) nb')
            ->addSelect('(SELECT sum(tr.TreatmentTime) 
                        FROM IndicateursBundle\Entity\Indic_TRSB tr 
                        LEFT JOIN IndicateursBundle\Entity\Indic_items it WITH tr.Indic_items = it.id 
                        WHERE YEAR(tr.openDate) = :year and it.requestNature LIKE :requestNature) delaiTotal')
            ->leftJoin("t.Indic_items",'i')
            ->where('YEAR(t.openDate) = :year')
            ->orderBy('t.openDate', 'ASC')
            ->setParameter('requestNature', $requestNature)
            ->setParameter('year', $year);

        $query = $this->projectFiltre($query,$project);
        $query = $this->monthFiltre($query,$month);
        $query = $this->natureFiltre($query,$requestNature);

        return $query->getQuery()->getOneOrNullResult();
    }
    /**
     * Delai de reponse des tickets support
     *
     * @param $year
     * @param int $project
     * @param int $month
     * @return mixed
     */
    public function delaireponseSupport($year,$project=-1,$month=-1){
        $requestNature  = "support";

        $query          = $this->createQueryBuilder('t');
        $query->select('count(t.id) nb')
            ->addSelect('(SELECT sum(tr.ResponseTime) 
                        FROM IndicateursBundle\Entity\Indic_TRSB tr 
                        LEFT JOIN IndicateursBundle\Entity\Indic_items it WITH tr.Indic_items = it.id 
                        WHERE YEAR(tr.openDate) = :year and it.requestNature LIKE :requestNature) delaiTotal')
            ->leftJoin("t.Indic_items",'i')
            ->where('YEAR(t.openDate) = :year')
            ->orderBy('t.openDate', 'ASC')
            ->setParameter('requestNature', $requestNature)
            ->setParameter('year', $year);

        $query = $this->projectFiltre($query,$project);
        $query = $this->monthFiltre($query,$month);
        $query = $this->natureFiltre($query,$requestNature);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Permet de récupérer un temps passé total sur un nombre de ticket.
     *
     * @param string $field
     *  Le champ date sur lequel appliqué le temps passé.
     * @param string $requestNature
     *  La nature du ticket sur le lequel on se base.
     * @param array $t_delai
     *  Permet de filtre sur le temps désiré.
     * @param array $t_filtre
     *  Filtre sur l'année, le mois, le projet et ou la priorité
     * @param string $select
     *  Permet de selectionner soit le total et la somme soit la liste des tickets
     * @return \Doctrine\ORM\QueryBuilder|null|array
     */
    public function delai($field,$requestNature,$t_delai,$t_filtre,$select ="nb"){
        $year       = "";
        $month      = "";
        $project    = "";
        $priority   = "";
        $time       = "";
        $operator   = "";

        //Valeurs obligatoire sans quoi la requete n'a plus de sens
        if(!$requestNature or !$field){
            return null;
        }
        //As t'on une selection à faire sur un temps en particulier
        if (!empty($t_delai)){
            if (array_key_exists('time',$t_delai)){
                $time = $t_delai['time'];
            }
            if (array_key_exists('operator',$t_delai)){
                $operator = $t_delai['operator'];
            }
        }
        //On regarde si on doit filtrer sur certains champs
        if(!empty($t_filtre)){
            if (array_key_exists('year',$t_filtre)){
                $year = $t_filtre['year'];
            }
            if (array_key_exists('month',$t_filtre)){
                $month = $t_filtre['month'];
            }
            if (array_key_exists('project',$t_filtre)){
                $project = $t_filtre['project'];
            }
            if (array_key_exists('priority',$t_filtre)){
                $priority = $t_filtre['priority'];
            }

        }
        $query          = $this->createQueryBuilder('t');
        switch ($select){
            case 'count':
            case '':
                $query->select('count(t.id) count');
                break;
            case 'sum':
                $query->select('sum(t.'.$field.') sum');
                break;
            case 'liste':
                $query->select('MONTH(t.openDate) mois,t.correctedDate,t.closedDate,t.ResponseTime, i.jtracId,i.projectId,i.requestNature,i.priority');
                break;
        }

        $query->leftJoin("t.Indic_items",'i')
            ->where('YEAR(t.openDate) = :year')
            ->orderBy('t.openDate', 'ASC')
            ->setParameter('year', $year);

        $query = $this->delaiSelector($query,$field,$time,$operator);
        $query = $this->projectFiltre($query,$project);
        $query = $this->monthFiltre($query,$month,$field);
        $query = $this->natureFiltre($query,$requestNature);
        $query = $this->priorityFiltre($query,$priority);

        switch ($select){
            case 'nb':
            case 'sum':
            case '':
                return $query->getQuery()->getOneOrNullResult();
                break;
            case 'liste':
                return $query->getQuery()->getArrayResult();
                break;
        }
    }

    /**
     * Donne le nombre de ticket par mois, avec le mois d'origine, qui ne sont pas corrigé ni clos.
     *
     * @param $year
     * @param $month
     * @param boolean $detail
     * @return array
     */
    public function evolutionNBTicket($year,$month,$detail = False){
        $month = $month+1;
        $date = $year."-".$month."-"."01";

        $select = 'SELECT YEAR(t.openDate) annee, MONTH(t.openDate) mois, count(t.openDate) nombre ';
        $groupBy = 'GROUP BY annee, mois';
        $orderBy = 'ORDER BY annee DESC, mois DESC';
        if($detail){
            $select = 'SELECT YEAR(t.openDate) annee, MONTH(t.openDate) mois, i.projectId projet,i.priority priority, i.requestNature nature,i.status status,i.jtracId jtracid ';
            $groupBy = '';
            $orderBy = 'ORDER BY annee ASC, mois ASC';
        }

        $query = $this->getEntityManager()->createQuery($select.'
            FROM IndicateursBundle\Entity\Indic_TRSB t
            LEFT JOIN IndicateursBundle\Entity\Indic_Items i
            WITH t.Indic_items = i.id
            where t.openDate < :date 
            AND ((t.correctedDate IS NULL 
                 or t.correctedDate >= :date)
                 AND (t.closedDate IS NULL
                 or t.closedDate >= :date)) 
            '.$groupBy.' '.$orderBy
        )->setParameter('date', $date);

        return $query->getArrayResult();
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
     * Permet de selectionner des tickets par rappport a un delai.
     *
     * @param \Doctrine\ORM\QueryBuilder
     * @param string $field
     * @param string $time
     * @param string $operator
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function delaiSelector($query,$field,$time,$operator){
        /* @var \Doctrine\ORM\QueryBuilder $query */
        if($field && $time && $operator){
            $query->andWhere('t.'.$field.' '.$operator.' :time')
                ->setParameter('time',$time);
        }

        return $query;
    }

    /**
     * Filtre sur un projet.
     *
     * @param \Doctrine\ORM\QueryBuilder $query
     * @param string $project
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
     * Filtre sur une année.
     *
     * @param \Doctrine\ORM\QueryBuilder $query
     * @param int $year
     * @param string $field
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function yearFiltre($query,$year,$field = 'correctedDate'){
        /* @var \Doctrine\ORM\QueryBuilder $query */
        if($year !=-1 && $year != 'all' && $year != Null){
            $query->andWhere('YEAR(t.'.$field.') = :year')
                ->setParameter('year',$year);
        }

        return $query;
    }

    /**
     * Filtre sur un mois.
     *
     * @param \Doctrine\ORM\QueryBuilder $query
     * @param string $month
     * @param string $field
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function monthFiltre($query,$month,$field = 'correctedDate'){
        /* @var \Doctrine\ORM\QueryBuilder $query */
        if($month !=-1 && $month != 'all' && $month != Null){
            $query->andWhere('MONTH(t.'.$field.') = :month')
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

    /**
     * Permet de ne garder que les ticket ouvert et en cours.
     *
     * @param $query
     * @param $status
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function notClosedCorrectedFiltre($query,$status){
        /* @var \Doctrine\ORM\QueryBuilder $query */
        if($status !=-1 && $status != 'all' && $status != Null){
            $query->andWhere('t.closedDate IS NULL');
            $query->andWhere('t.correctedDate IS NULL');
        }

        return $query;
    }

    /**
     * Donne la liste des mois et années de tous les tickets créés.
     *
     * @return array
     */
    public function getListeDateTickets(){
        $query = $this->createQueryBuilder('t');
        $query->select('YEAR(t.openDate) annee, MONTH(t.openDate) mois')
            ->groupBy('annee')
            ->addGroupBy('mois')
            ->where('t.openDate IS NOT NULL')
            ->orderBy('t.openDate', 'ASC');

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Donna la date la plus récente des ticket créés.
     *
     * @return array
     */
    public function getLastDate(){
        $query = $this->createQueryBuilder('t');
        $query->select('YEAR(t.openDate) annee, MONTH(t.openDate) mois')
            ->groupBy('annee')
            ->addGroupBy('mois')
            ->where('t.openDate IS NOT NULL')
            ->orderBy('t.openDate', 'DESC')
            ->setMaxResults(1);

        return $query->getQuery()->getArrayResult();
    }
}
