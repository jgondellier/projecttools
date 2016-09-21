<?php

namespace GanttBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tasks
 *
 * @ORM\Table(name="gantt_tasks")
 * @ORM\Entity(repositoryClass="GanttBundle\Repository\TasksRepository")
 */
class Gantt_tasks
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=100, nullable=true)
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="smallint", nullable=true)
     */
    private $level;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetime")
     */
    private $start;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="smallint")
     */
    private $duration;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime", nullable=true)
     */
    private $end;

    /**
     * @var bool
     *
     * @ORM\Column(name="startIsMilestone", type="boolean")
     */
    private $startIsMilestone;

    /**
     * @var bool
     *
     * @ORM\Column(name="endIsMilestone", type="boolean")
     */
    private $endIsMilestone;

    /**
     * @var string
     *
     * @ORM\Column(name="depends", type="string", length=255, nullable=true)
     */
    private $depends;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="progress", type="smallint")
     */
    private $progress;


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
     * Set name
     *
     * @param string $name
     * @return Tasks
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
     * Set code
     *
     * @param string $code
     * @return Tasks
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set level
     *
     * @param integer $level
     * @return Tasks
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Tasks
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     * @return Tasks
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime 
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Tasks
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return Tasks
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime 
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set startIsMilestone
     *
     * @param boolean $startIsMilestone
     * @return Tasks
     */
    public function setStartIsMilestone($startIsMilestone)
    {
        $this->startIsMilestone = $startIsMilestone;

        return $this;
    }

    /**
     * Get startIsMilestone
     *
     * @return boolean 
     */
    public function getStartIsMilestone()
    {
        return $this->startIsMilestone;
    }

    /**
     * Set endIsMilestone
     *
     * @param boolean $endIsMilestone
     * @return Tasks
     */
    public function setEndIsMilestone($endIsMilestone)
    {
        $this->endIsMilestone = $endIsMilestone;

        return $this;
    }

    /**
     * Get endIsMilestone
     *
     * @return boolean 
     */
    public function getEndIsMilestone()
    {
        return $this->endIsMilestone;
    }

    /**
     * Set depends
     *
     * @param string $depends
     * @return Tasks
     */
    public function setDepends($depends)
    {
        $this->depends = $depends;

        return $this;
    }

    /**
     * Get depends
     *
     * @return string 
     */
    public function getDepends()
    {
        return $this->depends;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Tasks
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
     * Set progress
     *
     * @param integer $progress
     * @return Tasks
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Get progress
     *
     * @return integer 
     */
    public function getProgress()
    {
        return $this->progress;
    }
}
