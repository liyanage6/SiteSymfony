<?php

namespace NL\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Advert
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="NL\PlatformBundle\Entity\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Advert
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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var boolean
     * 
     * @ORM\Column(name="published", type="boolean")
     */
    private $published = true;

    /**
     * @ORM\OneToOne(targetEntity="NL\PlatformBundle\Entity\Image", cascade={"persist"})
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity="NL\PlatformBundle\Entity\Category", cascade={"persist"})
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="NL\PlatformBundle\Entity\Application", mappedBy="advert")
     */
    private $applications;

    /**
     * @var
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(name="nb_applications", type="integer")
     */
    private $nbApplications = 0;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->categories = new ArrayCollection();
        $this->applications = new ArrayCollection();
    }

    public function increaseApplication()
    {
        $this->nbApplications++;
    }

    public function decreaseApplication()
    {
        $this->nbApplications--;
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateDate()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Set image
     *
     * @param \NL\PlatformBundle\Entity\Image $image
     *
     * @return Advert
     */
    public function setImage(Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \NL\PlatformBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }
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
     *
     * @return Advert
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
     * Set title
     *
     * @param string $title
     *
     * @return Advert
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Advert
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Advert
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Add category
     *
     * @param Category $category
     *
     * @return Advert
     */
    public function addCategory(Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param Category $category
     */
    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add application
     *
     * @param Application $application
     *
     * @return Advert
     */
    public function addApplication(Application $application)
    {
        $this->applications[] = $application;

        // On lie l'annonce à la candidature
        $application->setAdvert($this);

        return $this;
    }

    /**
     * Remove application
     *
     * @param Application $application
     */
    public function removeApplication(Application $application)
    {
        $this->applications->removeElement($application);

        // Et si notre relation était facultative (nullable=true, ce qui n'est pas notre cas ici attention) :
        // $application->setAdvert(null);
    }

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Advert
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Advert
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set nbApplications
     *
     * @param integer $nbApplications
     *
     * @return Advert
     */
    public function setNbApplications($nbApplications)
    {
        $this->nbApplications = $nbApplications;

        return $this;
    }

    /**
     * Get nbApplications
     *
     * @return integer
     */
    public function getNbApplications()
    {
        return $this->nbApplications;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return Advert
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }
}
