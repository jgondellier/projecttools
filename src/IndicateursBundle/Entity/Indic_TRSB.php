<?php

namespace IndicateursBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Indic_TRSB
 *
 * @ORM\Table(name="indic_trsb")
 * @ORM\Entity(repositoryClass="IndicateursBundle\Repository\Indic_TRSBRepository")
 */
class Indic_TRSB
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
     * @var \DateTime
     *
     * @ORM\Column(name="knowledge_date", type="datetime", nullable=true)
     */
    private $knowledgeDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="answer_date", type="datetime", nullable=true)
     */
    private $answerDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="first_corrected_date", type="datetime", nullable=true)
     */
    private $firstCorrectedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="corrected_date", type="datetime", nullable=true)
     */
    private $correctedDate;

    /**
     * @var int
     *
     * @ORM\Column(name="refused_count", type="smallint", nullable=true)
     */
    private $refusedCount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="treatment_time", type="datetime", nullable=true)
     */
    private $TreatmentTime;

    /**
     * @ORM\OneToOne(targetEntity="IndicateursBundle\Entity\Indic_items")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Indic_items;

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
     * Set knowledgeDate
     *
     * @param \DateTime $knowledgeDate
     * @return Indic_TRSB
     */
    public function setKnowledgeDate($knowledgeDate)
    {
        $this->knowledgeDate = $knowledgeDate;

        return $this;
    }

    /**
     * Get knowledgeDate
     *
     * @return \DateTime 
     */
    public function getKnowledgeDate()
    {
        return $this->knowledgeDate;
    }

    /**
     * Set answerDate
     *
     * @param \DateTime $answerDate
     * @return Indic_TRSB
     */
    public function setAnswerDate($answerDate)
    {
        $this->answerDate = $answerDate;

        return $this;
    }

    /**
     * Get answerDate
     *
     * @return \DateTime 
     */
    public function getAnswerDate()
    {
        return $this->answerDate;
    }

    /**
     * Set correctedDate
     *
     * @param \DateTime $correctedDate
     * @return Indic_TRSB
     */
    public function setCorrectedDate($correctedDate)
    {
        $this->correctedDate = $correctedDate;

        return $this;
    }

    /**
     * Get correctedDate
     *
     * @return \DateTime 
     */
    public function getCorrectedDate()
    {
        return $this->correctedDate;
    }

    /**
     * Set Indic_items
     *
     * @param \IndicateursBundle\Entity\Indic_items $indicItems
     * @return Indic_history
     */
    public function setIndicItems(\IndicateursBundle\Entity\Indic_items $indicItems)
    {
        $this->Indic_items = $indicItems;

        return $this;
    }

    /**
     * Get Indic_items
     *
     * @return \IndicateursBundle\Entity\Indic_items
     */
    public function getIndicItems()
    {
        return $this->Indic_items;
    }

    /**
     * Set refusedCount
     *
     * @param integer $refusedCount
     * @return Indic_TRSB
     */
    public function setRefusedCount($refusedCount)
    {
        $this->refusedCount = $refusedCount;

        return $this;
    }

    /**
     * Get refusedCount
     *
     * @return integer 
     */
    public function getRefusedCount()
    {
        return $this->refusedCount;
    }

    /**
     * Set TreatmentTime
     *
     * @param \DateTime $treatmentTime
     * @return Indic_TRSB
     */
    public function setTreatmentTime($treatmentTime)
    {
        $this->TreatmentTime = $treatmentTime;

        return $this;
    }

    /**
     * Get TreatmentTime
     *
     * @return \DateTime 
     */
    public function getTreatmentTime()
    {
        return $this->TreatmentTime;
    }

    /**
     * Set firstCorrectedDate
     *
     * @param \DateTime $firstCorrectedDate
     * @return Indic_TRSB
     */
    public function setFirstCorrectedDate($firstCorrectedDate)
    {
        $this->firstCorrectedDate = $firstCorrectedDate;

        return $this;
    }

    /**
     * Get firstCorrectedDate
     *
     * @return \DateTime 
     */
    public function getFirstCorrectedDate()
    {
        return $this->firstCorrectedDate;
    }
}
