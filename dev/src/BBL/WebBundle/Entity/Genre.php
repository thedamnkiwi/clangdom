<?php

namespace BBL\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Genre
 *
 * @ORM\Table(name="genre")
 * @ORM\Entity
 */
class Genre
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idGenre", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idgenre;

    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=45, nullable=false)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Artist", mappedBy="genregenre")
     */
    private $artistartist;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->artistartist = new \Doctrine\Common\Collections\ArrayCollection();
    }
    

    /**
     * Get idgenre
     *
     * @return integer 
     */
    public function getIdgenre()
    {
        return $this->idgenre;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Genre
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
     * Add artistartist
     *
     * @param \BBL\WebBundle\Entity\Artist $artistartist
     * @return Genre
     */
    public function addArtistartist(\BBL\WebBundle\Entity\Artist $artistartist)
    {
        $this->artistartist[] = $artistartist;
    
        return $this;
    }

    /**
     * Remove artistartist
     *
     * @param \BBL\WebBundle\Entity\Artist $artistartist
     */
    public function removeArtistartist(\BBL\WebBundle\Entity\Artist $artistartist)
    {
        $this->artistartist->removeElement($artistartist);
    }

    /**
     * Get artistartist
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArtistartist()
    {
        return $this->artistartist;
    }
}