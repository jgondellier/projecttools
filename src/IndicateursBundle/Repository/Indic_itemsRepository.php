<?php

namespace IndicateursBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Indic_itemsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Indic_itemsRepository extends EntityRepository
{
    /**
     * Recherche un Item avec son JtracId
     *
     * @param $itemId
     * @return mixed
     */
    public function getItemByItemId($itemId)
    {
        $query = $this->createQueryBuilder('i');
        //$query->select('i.itemId');
        $query->where('i.itemId = :itemId')
            ->setParameter('itemId', $itemId);

        return $query->getQuery()->getOneOrNullResult();
    }

    public function getCountAnomalieItemBydateAndPriority()
    {
        //Bug = Request Nature = 1
        $requestNature = 1;
        $query = $this->createQueryBuilder('i');
        $query->where('i.requestNature = :requestNature')
            ->setParameter('requestNature', $requestNature);

        /*SELECT * FROM `indic_items` i left join `indic_history` h ON i.item_id = h.indic_items_id WHERE i.request_nature = 1*/

        return $query->getQuery()->getArrayResult();
    }

    public function getItemsArray()
    {
        $query = $this->createQueryBuilder('i');
        $query->select('i.id,i.jtracId,i.projectId,i.createdDate,i.createdBy,i.status,i.severity,i.priority,i.requestNature,i.cadre,h')
            ->innerJoin('IndicateursBundle:Indic_history', 'h', 'WITH', 'i.id = h.Indic_items')
            ->orderBy('i.createdDate', 'DESC');

        $t_result = $query->getQuery()->getArrayResult();

        $t_indic = array();
        foreach($t_result as $result){
            if(!isset($result['0'])){
                var_dump($result);exit;
            }

            $t_history = $result['0'];
            if(isset($t_indic[$result['id']])){
                array_push($t_indic[$result['id']]['history'],$t_history);
            }else{
                $t_indic[$result['id']] = array('id'=>$result['id'],'jtracId'=>$result['jtracId'],'projectId'=>$result['projectId'],'createdDate'=>$result['createdDate'],'createdBy'=>$result['createdBy'],'status'=>$result['status'],'severity'=>$result['severity'],'priority'=>$result['priority'],'requestNature'=>$result['requestNature'],'cadre'=>$result['cadre']);
                $t_indic[$result['id']]['history'][0] = $t_history;
            }


        }

        //var_dump($query->getQuery()->getArrayResult());
        var_dump($t_indic);
        exit;

        return $query->getQuery()->getArrayResult();
    }
}
