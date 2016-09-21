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
}
