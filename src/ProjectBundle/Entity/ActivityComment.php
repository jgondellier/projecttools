<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityComment
 *
 * @ORM\Table(name="project_activity_comment")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\ActivityCommentRepository")
 */
class ActivityComment
{
    public function __construct()
    {
        $this->auteur = "Jonathan Gondellier";
        $this->dateCreation = new \Datetime();
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
     * @var string
     *
     * @ORM\Column(name="Libelle", type="string", length=255)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="Auteur", type="string", length=255, nullable=true)
     */
    private $auteur;

    /**
     * @ORM\ManyToOne(targetEntity="ProjectBundle\Entity\Activity", inversedBy="activityComments")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
     */
    private $activity;

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
     * @return ActivityComment
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
     * Set libelle
     *
     * @param string $libelle
     * @return ActivityComment
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
     * Set auteur
     *
     * @param string $auteur
     * @return ActivityComment
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return string 
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set activity
     *
     * @param \ProjectBundle\Entity\Activity $activity
     * @return ActivityComment
     */
    public function setActivity(\ProjectBundle\Entity\Activity $activity = null)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity
     *
     * @return \ProjectBundle\Entity\Activity 
     */
    public function getActivity()
    {
        return $this->activity;
    }
}
