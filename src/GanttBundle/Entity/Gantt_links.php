<?php

namespace GanttBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gantt_links
 *
 * @ORM\Table(name="gantt_links")
 * @ORM\Entity(repositoryClass="GanttBundle\Repository\Gantt_linksRepository")
 */
class Gantt_links
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
     * @ORM\Column(name="source", type="integer")
     */
    private $source;

    /**
     * @var int
     *
     * @ORM\Column(name="target", type="integer")
     */
    private $target;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;


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
     * Set source
     *
     * @param integer $source
     * @return Gantt_links
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return integer 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set target
     *
     * @param integer $target
     * @return Gantt_links
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return integer 
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Gantt_links
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }
}
