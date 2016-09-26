<?php

namespace IndicateursBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * History
 *
 * @ORM\Table(name="Indic_history")
 * @ORM\Entity(repositoryClass="IndicateursBundle\Repository\HistoryRepository")
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    private $createdDate;

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