<?php

namespace IndicateursBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * History
 *
 * @ORM\Table(name="Indic_history")
 * @ORM\Entity(repositoryClass="IndicateursBundle\Repository\Indic_historyRepository")
 */
class Indic_history
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="history_id", type="integer")
     */
    private $historyId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="qualified_date", type="datetime", nullable=true)
     */
    private $qualifiedDate;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;

    /**
     * @var int
     *
     * @ORM\Column(name="assigned_to", type="integer", nullable=true)
     */
    private $assignedTo;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="request_nature", type="smallint", nullable=true)
     */
    private $requestNature;

    /**
     * @ORM\ManyToOne(targetEntity="IndicateursBundle\Entity\Indic_items")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Indic_items;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set historyId
     *
     * @param integer $historyId
     * @return Indic_history
     */
    public function setHistoryId($historyId)
    {
        $this->historyId = $historyId;

        return $this;
    }

    /**
     * Get historyId
     *
     * @return integer
     */
    public function getHistoryId()
    {
        return $this->historyId;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Indic_history
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime 
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set qualifiedDate
     *
     * @param \DateTime $qualifiedDate
     * @return Indic_history
     */
    public function setQualifiedDate($qualifiedDate)
    {
        $this->qualifiedDate = $qualifiedDate;

        return $this;
    }

    /**
     * Get qualifiedDate
     *
     * @return \DateTime
     */
    public function getQualifiedDate()
    {
        return $this->qualifiedDate;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     * @return Indic_history
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set assignedTo
     *
     * @param integer $assignedTo
     * @return Indic_history
     */
    public function setAssignedTo($assignedTo)
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    /**
     * Get assignedTo
     *
     * @return integer 
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Indic_history
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set requestNature
     *
     * @param integer $requestNature
     * @return Indic_history
     */
    public function setRequestNature($requestNature)
    {
        $this->requestNature = $requestNature;

        return $this;
    }

    /**
     * Get requestNature
     *
     * @return integer 
     */
    public function getRequestNature()
    {
        return $this->requestNature;
    }

    /**
     * Set Indic_items
     *
     * @param \IndicateursBundle\Entity\Indic_items $indicItems
     * @return Indic_history
     */
    public function setIndicItems(\IndicateursBundle\Entity\Indic_items $indicItems)
    {
        $this->Indic_items = $indicItems;

        return $this;
    }

    /**
     * Get Indic_items
     *
     * @return \IndicateursBundle\Entity\Indic_items 
     */
    public function getIndicItems()
    {
        return $this->Indic_items;
    }
}
