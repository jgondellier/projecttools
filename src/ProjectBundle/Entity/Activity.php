<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Activity
 *
 * @ORM\Table(name="project_activity")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\ActivityRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Activity
{
    public function __construct()
    {
        $this->dateCreation = new \Datetime();
        $this->activityComments        = new ArrayCollection();
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateDate()
    {
        $this->dateModification = new \Datetime();
    }

    public function __toString() {
        return $this->libelle;
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
     * @ORM\Column(name="DateResolution", type="datetime", nullable=true)
     */
    private $dateResolution;

    /**
     * @var string
     *
     * @ORM\Column(name="CadreContractuel", type="string", length=255)
     */
    private $cadreContractuel;

    /**
     * @var string
     *
     * @ORM\Column(name="Libelle", type="string", length=255)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="Etat", type="string", length=20)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity="ProjectBundle\Entity\Project", inversedBy="activitys")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity="ProjectBundle\Entity\ActivityComment", mappedBy="activity", orphanRemoval=true)
     */
    private $activityComments;

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
     * @return Activity
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
     * @return Activity
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
     * Set dateResolution
     *
     * @param \DateTime $dateResolution
     * @return Activity
     */
    public function setDateResolution($dateResolution)
    {
        $this->dateResolution = $dateResolution;

        return $this;
    }

    /**
     * Get dateResolution
     *
     * @return \DateTime 
     */
    public function getDateResolution()
    {
        return $this->dateResolution;
    }

    /**
     * Set cadreContractuel
     *
     * @param string $cadreContractuel
     * @return Activity
     */
    public function setCadreContractuel($cadreContractuel)
    {
        $this->cadreContractuel = $cadreContractuel;

        return $this;
    }

    /**
     * Get cadreContractuel
     *
     * @return string 
     */
    public function getCadreContractuel()
    {
        return $this->cadreContractuel;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return Activity
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set etat
     *
     * @param string $etat
     * @return Activity
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
     * @return Activity
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
}
