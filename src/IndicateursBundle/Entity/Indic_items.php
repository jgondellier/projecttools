<?php

namespace IndicateursBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Items
 *
 * @ORM\Table(name="Indic_items")
 * @ORM\Entity(repositoryClass="IndicateursBundle\Repository\Indic_itemsRepository")
 */
class Indic_items
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
     * @ORM\Column(name="item_id", type="integer")
     */
    private $itemId;

    /**
     * @var int
     *
     * @ORM\Column(name="jtrac_id", type="integer")
     */
    private $jtracId;

    /**
     * @var int
     *
     * @ORM\Column(name="project_id", type="integer")
     */
    private $projectId;

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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="severity", type="smallint", nullable=true)
     */
    private $severity;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="smallint", nullable=true)
     */
    private $priority;

    /**
     * @var int
     *
     * @ORM\Column(name="request_nature", type="smallint", nullable=true)
     */
    private $requestNature;

    /**
     * @var int
     *
     * @ORM\Column(name="cadre", type="smallint", nullable=true)
     */
    private $cadre;

    /**
     * @var boolean
     *
     * @ORM\Column(name="trsb", type="boolean", nullable=true)
     */
    private $trsb;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="trsb_date", type="datetime", nullable=true)
     */
    private $TRSBDate;

    /**
     * @var float
     *
     * @ORM\Column(name="delai", type="float", nullable=true)
     */
    private $delai;

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
     * Set itemId
     *
     * @param integer $itemId
     * @return Indic_items
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get itemId
     *
     * @return integer
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set jtracId
     *
     * @param integer $jtracId
     * @return Indic_items
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
     * Set projectId
     *
     * @param integer $projectId
     * @return Indic_items
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Get projectId
     *
     * @return integer 
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Indic_items
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
     * @return Indic_items
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
     * Set title
     *
     * @param string $title
     * @return Indic_items
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Indic_items
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Indic_items
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
     * Set severity
     *
     * @param integer $severity
     * @return Indic_items
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;

        return $this;
    }

    /**
     * Get severity
     *
     * @return integer 
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return Indic_items
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set requestNature
     *
     * @param integer $requestNature
     * @return Indic_items
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
     * Set cadre
     *
     * @param integer $cadre
     * @return Indic_items
     */
    public function setCadre($cadre)
    {
        $this->cadre = $cadre;

        return $this;
    }

    /**
     * Get cadre
     *
     * @return integer 
     */
    public function getCadre()
    {
        return $this->cadre;
    }

    /**
     * Set trsb
     *
     * @param boolean $trsb
     * @return Indic_items
     */
    public function setTrsb($trsb)
    {
        $this->trsb = $trsb;

        return $this;
    }

    /**
     * Get trsb
     *
     * @return boolean
     */
    public function getTrsb()
    {
        return $this->trsb;
    }

    /**
     * Set TRSBDate
     *
     * @param \DateTime $tRSBDate
     * @return Indic_items
     */
    public function setTRSBDate($tRSBDate)
    {
        $this->TRSBDate = $tRSBDate;

        return $this;
    }

    /**
     * Get TRSBDate
     *
     * @return \DateTime 
     */
    public function getTRSBDate()
    {
        return $this->TRSBDate;
    }

    /**
     * Set delai
     *
     * @param integer $delai
     * @return Indic_items
     */
    public function setDelai($delai)
    {
        $this->delai = $delai;

        return $this;
    }

    /**
     * Get delai
     *
     * @return integer 
     */
    public function getDelai()
    {
        return $this->delai;
    }
}
