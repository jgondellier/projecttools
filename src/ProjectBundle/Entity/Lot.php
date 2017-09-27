<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lot
 *
 * @ORM\Table(name="project_lot")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\LotRepository")
 */
class Lot
{
    public function __construct()
    {
        $this->dateCreation = new \Datetime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateDate()
    {
        $this->dateModification = new \Datetime();
    }
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
     * @ORM\Column(name="DateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateModification", type="datetime", nullable=true)
     */
    private $dateModification;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateRecette", type="datetime", nullable=true)
     */
    private $dateRecette;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DatePreprod", type="datetime", nullable=true)
     */
    private $datePreprod;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateProd", type="datetime", nullable=true)
     */
    private $dateProd;

    /**
     * @var string
     *
     * @ORM\Column(name="Version", type="string", length=255, nullable=true)
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="Description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="Etat", type="string", length=20)
     */
    private $etat;

    /**
     * @var boolean
     *
     * @ORM\Column(name="recette", type="boolean", nullable=true)
     */
    private $recette;

    /**
     * @var boolean
     *
     * @ORM\Column(name="preprod", type="boolean", nullable=true)
     */
    private $preprod;

    /**
     * @var boolean
     *
     * @ORM\Column(name="prod", type="boolean", nullable=true)
     */
    private $prod;

    /**
     * @ORM\ManyToOne(targetEntity="ProjectBundle\Entity\Project", inversedBy="lots")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Lot
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     * @return Lot
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime 
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set version
     *
     * @param string $version
     * @return Lot
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Lot
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
     * Set etat
     *
     * @param string $etat
     * @return Lot
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set project
     *
     * @param \ProjectBundle\Entity\Project $project
     * @return Lot
     */
    public function setProject(\ProjectBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \ProjectBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set recette
     *
     * @param boolean $recette
     *
     * @return Lot
     */
    public function setRecette($recette)
    {
        $this->recette = $recette;

        return $this;
    }

    /**
     * Get recette
     *
     * @return boolean
     */
    public function getRecette()
    {
        return $this->recette;
    }

    /**
     * Set preprod
     *
     * @param boolean $preprod
     *
     * @return Lot
     */
    public function setPreprod($preprod)
    {
        $this->preprod = $preprod;

        return $this;
    }

    /**
     * Get preprod
     *
     * @return boolean
     */
    public function getPreprod()
    {
        return $this->preprod;
    }

    /**
     * Set prod
     *
     * @param boolean $prod
     *
     * @return Lot
     */
    public function setProd($prod)
    {
        $this->prod = $prod;

        return $this;
    }

    /**
     * Get prod
     *
     * @return boolean
     */
    public function getProd()
    {
        return $this->prod;
    }

    /**
     * Set dateRecette
     *
     * @param \DateTime $dateRecette
     *
     * @return Lot
     */
    public function setDateRecette($dateRecette)
    {
        $this->dateRecette = $dateRecette;

        return $this;
    }

    /**
     * Get dateRecette
     *
     * @return \DateTime
     */
    public function getDateRecette()
    {
        return $this->dateRecette;
    }

    /**
     * Set datePreprod
     *
     * @param \DateTime $datePreprod
     *
     * @return Lot
     */
    public function setDatePreprod($datePreprod)
    {
        $this->datePreprod = $datePreprod;

        return $this;
    }

    /**
     * Get datePreprod
     *
     * @return \DateTime
     */
    public function getDatePreprod()
    {
        return $this->datePreprod;
    }

    /**
     * Set dateProd
     *
     * @param \DateTime $dateProd
     *
     * @return Lot
     */
    public function setDateProd($dateProd)
    {
        $this->dateProd = $dateProd;

        return $this;
    }

    /**
     * Get dateProd
     *
     * @return \DateTime
     */
    public function getDateProd()
    {
        return $this->dateProd;
    }
}
