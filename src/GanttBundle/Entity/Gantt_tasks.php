<?php

namespace GanttBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gantt_tasks
 *
 * @ORM\Table(name="gantt_tasks")
 * @ORM\Entity(repositoryClass="GanttBundle\Repository\Gantt_tasksRepository")
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
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration= 0;

    /**
     * @var float
     *
     * @ORM\Column(name="progress", type="float")
     */
    private $progress= 0;

    /**
     * @var int
     *
     * @ORM\Column(name="sortorder", type="integer")
     */
    private $sortorder= 0;

    /**
     * @var int
     *
     * @ORM\Column(name="parent", type="integer")
     */
    private $parent;


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
     * Set text
     *
     * @param string $text
     * @return Gantt_tasks
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Gantt_tasks
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Gantt_tasks
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
     * Set progress
     *
     * @param float $progress
     * @return Gantt_tasks
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Get progress
     *
     * @return float 
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Set sortorder
     *
     * @param integer $sortorder
     * @return Gantt_tasks
     */
    public function setSortorder($sortorder)
    {
        $this->sortorder = $sortorder;

        return $this;
    }

    /**
     * Get sortorder
     *
     * @return integer 
     */
    public function getSortorder()
    {
        return $this->sortorder;
    }

    /**
     * Set parent
     *
     * @param integer $parent
     * @return Gantt_tasks
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return integer 
     */
    public function getParent()
    {
        return $this->parent;
    }
}
