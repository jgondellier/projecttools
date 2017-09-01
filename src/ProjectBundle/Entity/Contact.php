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
     * @ORM\Column(name="nom", type="string", length=100)
     */
    private $nom;

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
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="ProjectBundle\Entity\Project")
     * @ORM\JoinColumn(nullable=true)
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
     * Set nom
     *
     * @param string $nom
     * @return Contact
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
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
}
