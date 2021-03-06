<?php

namespace BBL\WebBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;

/**
 * File
 *
 * @ORM\Table(name="file")
 * @ORM\Entity
 */
class File
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idFile", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idfile;

    /**
     * @var string
     *
     * @ORM\Column(name="Path", type="string", length=45, nullable=true)
     */
    private $path;

    /**
     * Is no persistence field, field for filehandling
     */
    private $file;
    
    /**
     *  Is no persistence field, field for upload-root-Dir
     */
    protected static $uploadDirectory = null;
    
    
	
    /**
     * Get idfile
     *
     * @return integer 
     */
    public function getIdfile()
    {
        return $this->idfile;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return File
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }
    
    
    // Methodes for Path
    public function getAbsolutePath()
    {
    	return null === $this->path
    	? null
    	: $this->getUploadRootDir().$this->path;
    }
    
    public function getWebPath()
    {
    	return null === $this->path
    	? null
    	: $this->getUploadDir().$this->path;
    }
    
    protected function getUploadRootDir()
    {
    	// the absolute directory path where uploaded
    	// documents should be saved
    	return self::$uploadDirectory;
    }
    
    protected function getUploadDir()
    {
    	// get rid of the __DIR__ so it doesn't screw up
    	// when displaying uploaded doc/image in the view.
    	return 'uploads';
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
    
    /**
     *  Sets Upload Root dir (gets executed by Bundle-boot methode in BBLWebBundle)
     * @param string $dir
     */
    static public function setUploadsDirectory($dir)
    {
    	self::$uploadDirectory = $dir;
    }
    
    public function upload($filename = "", $path = "")
    {

    	// the file property can be empty if the field is not required
    	if (null === $this->getFile()) {
    		return;
    	}
    
    	// use the original file name here but you should
    	// sanitize it at least to avoid any security issues
    
    	// move takes the target directory and then the
    	// target filename to move to
    	if($filename == "")
    	{
    		$this->getFile()->move(
    			$this->getUploadRootDir().$path,
    			$this->getFile()->getClientOriginalName()
    		);
    	}
    	else
    	{
    		$this->getFile()->move(
    				$this->getUploadRootDir().$path,
    				$filename
    		);
    	}
    
    	// set the path property to the filename where you've saved the file
    	$this->path = $path."/".$filename;
    
    	// clean up the file property as you won't need it anymore
    	$this->file = null;
    }
    
    
    
}