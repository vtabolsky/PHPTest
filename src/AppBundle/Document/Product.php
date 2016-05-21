<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @MongoDB\Document
 * @MongoDBUnique(fields="name")
 */
class Product
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
	 * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @MongoDB\Float
     */
    protected $price;
	
    /**
     * @MongoDB\String
     */	
	protected $description;
	
    /**
     * @Assert\File(mimeTypes={ "image/jpeg","image/gif","image/png" }, maxSize="6000000")
     */	
	protected $file;
	
	/**
     * @MongoDB\Timestamp
     */	
	protected $createdOn;
	
	/**
     * @MongoDB\String
	 * @Assert\NotBlank()
     */	
	protected $tags;
	
	/**
     * @MongoDB\String
     */	
	protected $image;
	
	public function getId()
    {
        return $this->id;
    }
	
	public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
	
	public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }
	
	public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description=null)
    {
        $this->description = $description;
    }
	
	public function getImage()
    {
        return $this->image;
    }

    public function setImage($image=null)
    {
        $this->image = $image;
    }
	
	public function getCreatedOn()
    {
        return $this->createdOn;
    }

    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }
	
		public function getTags()
    {
        return $this->tags;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
    }
	
	    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }
    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
}