<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * project
 *
 * @ORM\Table(name="project_project")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\ProjectRepository")
 */
class Project
{
    public function __construct()
    {
        $this->environnements   = new ArrayCollection();
        $this->contacts         = new ArrayCollection();
    }
    public function __toString() {
        return $this->name;
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
     * @var string
     *
     * @ORM\Column(name="jtrac_id", type="string", length=50, nullable=true)
     */
    private $jtracId;

    /**
     * @var string
     *
     * @ORM\Column(name="jira_id", type="string", length=50, nullable=true)
     */
    private $jiraId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="sourcecode_url", type="string", length=255, nullable=true)
     */
    private $sourcecodeUrl;

    /**
     * @ORM\OneToMany(targetEntity="ProjectBundle\Entity\Environnement", mappedBy="project", cascade={"remove"})
     */
    private $environnements;

    /**
     * @ORM\OneToMany(targetEntity="ProjectBundle\Entity\Contact", mappedBy="project")
     */
    private $contacts;

    /**
     * @ORM\OneToMany(targetEntity="ProjectBundle\Entity\Activity", mappedBy="project")
     */
    private $activitys;


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
     * @return Project
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
     * Set jiraId
     *
     * @param integer $jiraId
     * @return Project
     */
    public function setJiraId($jiraId)
    {
        $this->jiraId = $jiraId;

        return $this;
    }

    /**
     * Get jiraId
     *
     * @return integer 
     */
    public function getJiraId()
    {
        return $this->jiraId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Project
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
     * Set sourcecodeUrl
     *
     * @param string $sourcecodeUrl
     * @return Project
     */
    public function setSourcecodeUrl($sourcecodeUrl)
    {
        $this->sourcecodeUrl = $sourcecodeUrl;

        return $this;
    }

    /**
     * Get sourcecodeUrl
     *
     * @return string 
     */
    public function getSourcecodeUrl()
    {
        return $this->sourcecodeUrl;
    }

    /**
     * Add environnements
     *
     * @param \ProjectBundle\Entity\Environnement $environnements
     * @return Project
     */
    public function addEnvironnement(\ProjectBundle\Entity\Environnement $environnements)
    {
        $this->environnements[] = $environnements;

        return $this;
    }

    /**
     * Remove environnements
     *
     * @param \ProjectBundle\Entity\Environnement $environnements
     */
    public function removeEnvironnement(\ProjectBundle\Entity\Environnement $environnements)
    {
        $this->environnements->removeElement($environnements);
    }

    /**
     * Get environnements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEnvironnements()
    {
        return $this->environnements;
    }

    /**
     * Add contacts
     *
     * @param \ProjectBundle\Entity\Contact $contacts
     * @return Project
     */
    public function addContact(\ProjectBundle\Entity\Contact $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts
     *
     * @param \ProjectBundle\Entity\Contact $contacts
     */
    public function removeContact(\ProjectBundle\Entity\Contact $contacts)
    {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Add activitys
     *
     * @param \ProjectBundle\Entity\Activity $activitys
     * @return Project
     */
    public function addActivity(\ProjectBundle\Entity\Activity $activitys)
    {
        $this->activitys[] = $activitys;

        return $this;
    }

    /**
     * Remove activitys
     *
     * @param \ProjectBundle\Entity\Activity $activitys
     */
    public function removeActivity(\ProjectBundle\Entity\Activity $activitys)
    {
        $this->activitys->removeElement($activitys);
    }

    /**
     * Get activitys
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivitys()
    {
        return $this->activitys;
    }
}
