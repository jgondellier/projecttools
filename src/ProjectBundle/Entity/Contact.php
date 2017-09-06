<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contact
 *
 * @ORM\Table(name="project_contact")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\ContactRepository")
 */
class Contact
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
     * @var int
     *
     * @ORM\Column(name="id_bnp", type="string", length=50)
     */
    private $idBnp;

    /**
     * @var int
     *
     * @ORM\Column(name="id_jtrac", type="string", length=50)
     */
    private $idJtrac;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=100)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=100, nullable=true)
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="ProjectBundle\Entity\Project", inversedBy="contacts")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="datetime", nullable=true)
     */
    private $dateModification;


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
     * Set idBnp
     *
     * @param integer $idBnp
     * @return Contact
     */
    public function setIdBnp($idBnp)
    {
        $this->idBnp = $idBnp;

        return $this;
    }

    /**
     * Get idBnp
     *
     * @return integer 
     */
    public function getIdBnp()
    {
        return $this->idBnp;
    }

    /**
     * Set idJtrac
     *
     * @param integer $idJtrac
     * @return Contact
     */
    public function setIdJtrac($idJtrac)
    {
        $this->idJtrac = $idJtrac;

        return $this;
    }

    /**
     * Get idJtrac
     *
     * @return integer 
     */
    public function getIdJtrac()
    {
        return $this->idJtrac;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Contact
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return Contact
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string 
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set mail
     *
     * @param string $mail
     * @return Contact
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string 
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Contact
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
     * Set Activity
     *
     * @param \ProjectBundle\Entity\Project $activity
     * @return Contact
     */
    public function setActivity(\ProjectBundle\Entity\Project $activity = null)
    {
        $this->Activity = $activity;

        return $this;
    }

    /**
     * Get Activity
     *
     * @return \ProjectBundle\Entity\Project
     */
    public function getActivity()
    {
        return $this->Activity;
    }

    /**
     * Set project
     *
     * @param \ProjectBundle\Entity\Project $project
     * @return Contact
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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Contact
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
     * @return Contact
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
}
