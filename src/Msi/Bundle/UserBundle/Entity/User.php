<?php

namespace Msi\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use FOS\UserBundle\Entity\User as BaseUser;
use Msi\Bundle\ImaginatorBundle\Imaginator\Imaginator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", name="updated_at")
     */
    protected $updatedAt;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $avatarName;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $avatarPath;

    /**
     * @Assert\File(maxSize="6000000")
     */
    protected $avatarFile;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $location;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $nickname;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $bio;

    /**
     * @ORM\Column(type="integer")
     */
    protected $position;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="users")
     */
    protected $team;

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->setEnabled(true);
    }

    public function __construct()
    {
        parent::__construct();

        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->position = 1;
    }

    public function getUploadDir()
    {
        return __DIR__.'/../../../../../web/uploads/users/avatars/';
    }

    public function removeFile()
    {
        $file = $this->getUploadDir().$this->avatarName;
        if (is_file($file)) unlink($file);

        $file = $this->getUploadDir().'t_'.$this->avatarName;
        if (is_file($file)) unlink($file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $this->removeFile();
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function postUpload()
    {
        if ($this->avatarFile === null) return;

        $this->avatarFile->move($this->getUploadDir(), $this->avatarName);

        $im = new Imaginator($this->getUploadDir().$this->avatarName);

        $im->resize(48, 48)->save();
        $im->resize(100, 100)->saveAs($this->getUploadDir().'t_'.$this->avatarName);

        unset($this->avatarFile);
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        $this->updatedAt = new \DateTime();

        if ($this->avatarFile === null) return;

        $ext = $this->avatarFile->guessExtension();

        if (!in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
            die('The file must be either jpg or png or gif.');
        }

        $this->removeFile();
        $this->avatarName = uniqid(time()).'.'.$ext;
        $this->avatarPath = '/uploads/users/avatars/';
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatarPath.$this->avatarName;
    }

    public function getAvatarName()
    {
        return $this->avatarName;
    }

    public function setAvatarName($avatarName)
    {
        $this->avatarName = $avatarName;

        return $this;
    }

    public function getAvatarPath()
    {
        return $this->avatarPath;
    }

    public function setAvatarPath($avatarPath)
    {
        $this->avatarPath = $avatarPath;

        return $this;
    }

    public function getAvatarFile()
    {
        return $this->avatarFile;
    }

    public function setAvatarFile($avatarFile)
    {
        $this->avatarFile = $avatarFile;
        $this->updatedAt = new \DateTime();

        return $this;
    }

    public function getNickname()
    {
        return $this->nickname;
    }

    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getBio()
    {
        return $this->bio;
    }

    public function setBio($bio)
    {
        $this->bio = $bio;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTeam()
    {
        return $this->team;
    }

    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }
}
