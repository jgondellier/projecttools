<?php

namespace IndicateursBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Indic_project
 *
 * @ORM\Table(name="indic_project")
 * @ORM\Entity(repositoryClass="IndicateursBundle\Repository\Indic_projectRepository")
 */
class Indic_project
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
     * @ORM\Column(name="project_id", type="smallint", unique=true)
     */
    private $projectId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code_url", type="string", length=50)
     */
    private $codeUrl;

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
     * Set projectId
     *
     * @param integer $projectId
     * @return Indic_project
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
     * Set name
     *
     * @param string $name
     * @return Indic_project
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
     * Set codeUrl
     *
     * @param string $codeUrl
     * @return Indic_project
     */
    public function setCodeUrl($codeUrl)
    {
        $this->codeUrl = $codeUrl;

        return $this;
    }

    /**
     * Get codeUrl
     *
     * @return string 
     */
    public function getCodeUrl()
    {
        return $this->codeUrl;
    }
}
