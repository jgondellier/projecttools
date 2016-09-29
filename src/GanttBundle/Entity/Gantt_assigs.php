<?php

namespace GanttBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gantt_assigs
 *
 * @ORM\Table(name="gantt_assigs")
 * @ORM\Entity(repositoryClass="GanttBundle\Repository\Gantt_assigsRepository")
 */
class Gantt_assigs
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
     * @ORM\Column(name="effort", type="integer", nullable=true)
     */
    private $effort;

    /**
     * @ORM\ManyToOne(targetEntity="GanttBundle\Entity\Gantt_resources")
     * @ORM\JoinColumn(nullable=true)
     */
    private $Gantt_resources;

    /**
     * @ORM\ManyToOne(targetEntity="GanttBundle\Entity\Gantt_roles")
     * @ORM\JoinColumn(nullable=true)
     */
    private $Gantt_roles;

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
     * Set effort
     *
     * @param integer $effort
     * @return Gantt_assigs
     */
    public function setEffort($effort)
    {
        $this->effort = $effort;

        return $this;
    }

    /**
     * Get effort
     *
     * @return integer 
     */
    public function getEffort()
    {
        return $this->effort;
    }

    /**
     * Set Gantt_resources
     *
     * @param \GanttBundle\Entity\Gantt_resources $ganttResources
     * @return Gantt_assigs
     */
    public function setGanttResources(\GanttBundle\Entity\Gantt_resources $ganttResources = null)
    {
        $this->Gantt_resources = $ganttResources;

        return $this;
    }

    /**
     * Get Gantt_resources
     *
     * @return \GanttBundle\Entity\Gantt_resources 
     */
    public function getGanttResources()
    {
        return $this->Gantt_resources;
    }

    /**
     * Set Gantt_roles
     *
     * @param \GanttBundle\Entity\Gantt_roles $ganttRoles
     * @return Gantt_assigs
     */
    public function setGanttRoles(\GanttBundle\Entity\Gantt_roles $ganttRoles = null)
    {
        $this->Gantt_roles = $ganttRoles;

        return $this;
    }

    /**
     * Get Gantt_roles
     *
     * @return \GanttBundle\Entity\Gantt_roles 
     */
    public function getGanttRoles()
    {
        return $this->Gantt_roles;
    }
}
