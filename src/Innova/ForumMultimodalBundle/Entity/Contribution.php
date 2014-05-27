<?php

namespace Innova\ForumMultimodalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contribution Entity
 * @category   Entity
 * @package    Innova
 * @author Mahmoud Charfeddine <[charfeddine.mahmoud@gmail.com]>
 * @copyright  2014 Mahmoud Charfeddine.
 * @version    0.1
 */

/**
 * Contribution
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Innova\ForumMultimodalBundle\Entity\ContributionRepository")
 */
class Contribution
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="time")
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="contents", type="text")
     */
    private $contents;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=255)
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(name="lien", type="string", length=255)
     */
    private $lien;

    /**
     * @var string
     *
     * @ORM\Column(name="father", type="string", length=255)
     */
    private $father;

    /**
     * @var string
     *
     * @ORM\Column(name="listen", type="string", length=255)
     */
    private $listen;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    /**
   * @ORM\ManyToOne(targetEntity="Innova\ForumMultimodalBundle\Entity\Subject")
   * @ORM\JoinColumn(nullable=false)
   */
    private $subject;


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
     * Set date
     *
     * @param \DateTime $date
     * @return Contribution
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return Contribution
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Contribution
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set contents
     *
     * @param string $contents
     * @return Contribution
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Get contents
     *
     * @return string 
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Set extension
     *
     * @param string $extension
     * @return Contribution
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get lien
     *
     * @return string 
     */
    public function getLien()
    {
        return $this->lien;
    }

    /**
     * Set lien
     *
     * @param string $lien
     * @return Contribution
     */
    public function setLien($lien)
    {
        $this->lien = $lien;

        return $this;
    }

    /**
     * Get father
     *
     * @return string 
     */
    public function getFather()
    {
        return $this->father;
    }

    /**
     * Set father
     *
     * @param string $father
     * @return Contribution
     */
    public function setFather($father)
    {
        $this->father = $father;

        return $this;
    }

    /**
     * Get listen
     *
     * @return string 
     */
    public function getListen()
    {
        return $this->listen;
    }

    /**
     * Set listen
     *
     * @param string $listen
     * @return Contribution
     */
    public function setListen($listen)
    {
        $this->listen = $listen;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }
    /**
     * Set user
     *
     * @param string $user
     * @return Contribution
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
       * Set subject
       *
       * @param Innova\ForumMultimodalBundle\Entity\Subject $subject
       */
    public function setSubject(\Innova\ForumMultimodalBundle\Entity\Subject $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
       * Get article
       *
       * @return Innova\ForumMultimodalBundle\Entity\Subject 
       */
    public function getSubject()
    {
        return $this->subject;
    }
}
