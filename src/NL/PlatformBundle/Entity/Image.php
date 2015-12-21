<?php

namespace NL\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="NL\PlatformBundle\Entity\ImageRepository")
 */
class Image
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
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;

    /**
     * @var UploadedFile
     */
    private $file;

    private $tempFileName;


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
     * Set url
     *
     * @param string $url
     *
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        if (null !==  $this->url) { // vérifie si on avait déjà un fichier pour cette entité
            $this->tempFileName = $this->url;// sauvegarde l'extension du fichier pour le supprimer plus tard

            $this->url = null; // Reinisialisé les valeurs des attribut alt et url
            $this->alt = null;
        }

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }


    /* ************************************
     *       START
     * FILE - Formualire
     *
     * Automatisation grace au Evenement
     *
     *
     **************************************/

    /**
     * ORM\PrePersist()
     * ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null === $this->file) {
            return;
        }

        $this->url = $this->file->guessExtension();

        $this->alt = $this->file->getClientOriginalName();
    }

    /**
     * ORM\PostPersist()
     * ORM\PostUpdate()
     */
    public function upload()
    {
        if (null == $this->file) {
            return;
        }

        //Si un ancien fichier existe, on le supprime
        if (null !== $this->tempFileName) {
            $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFileName;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        //Deplacer fichier envoyer dans repertoire
        $this->file->move(
            $this->getUploadRootDir(),
            $this->id.'.'.$this->url
        );
    }

    /**
     * ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        $this->tempFileName = $this->getUploadRootDir().'/'.$this->id.'.'.$this->url;
        // sauvegarde temporaire du nom du ficher, car il depend de l'id
    }

    /**
     * ORM\PostRemove()
     */
    public function removeUpload()
    {
        // en PostRemove on a plus d'id, on utilise notre tempFileName et on le supprime
        if (file_exists($this->tempFileName)) {
            unlink($this->tempFileName);
        }
    }

    /* ************************************
     *
     * FILE - Formualire
     *
     * Automatisation grace au Evenement
     *
     *          END
     **************************************/

    public function getUploadDir()
    {
        // Retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
        return 'uploads/img';
    }

    public function getUploadRootDir()
    {
        // Retourne le chemin absolu vers l'image pour notre code PHP
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    public function getWebPath()
    {
        return $this->getUploadDir().'/'.$this->getId().'.'.$this->getUrl();
    }
}

