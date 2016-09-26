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
     * @var int
     *
     * @ORM\Column(name="jtrac_id", type="integer")
     */
    private $jtracId;

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
     * @var string
     *
     * @ORM\Column(name="request_nature", type="smallint", nullable=true)
     */
    private $requestNature;


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
     * Set jtracId
     *
     * @param integer $jtracId
     * @return History
     */
    public function setJtracId($jtracId)
    {
        $this->jtracId = $jtracId;

        return $this;
    }

    /**
     * Get jtracId
     *
     * @return integer 
     */
    public function getJtracId()
    {
        return $this->jtracId;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return History
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
     * @return History
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
     * @return History
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
     * @param string $requestNature
     * @return History
     */
    public function setRequestNature($requestNature)
    {
        $this->requestNature = $requestNature;

        return $this;
    }

    /**
     * Get requestNature
     *
     * @return string 
     */
    public function getRequestNature()
    {
        return $this->requestNature;
    }
}
