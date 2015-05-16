<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 13/05/15
 * Time: 11:01
 */

namespace ConsultBundle\Utility;


use Aws\Common\Enum\Region;
use Aws\S3\S3Client;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;

class FileUploadUtil
{


    protected $s3ResourcesBucket;
    protected $s3Client;
    protected $fileName ="TREATMENTPHOTO";
    protected $tempUrl = "/tmp/practo-consult-resources/";
    protected $s3Key;
    protected $s3Secret;
    protected $scheme;
    protected $region;

    public function __construct($s3Key, $s3Secret, $region = 'ap-southeast-1', $scheme = 'https', $s3ResourceBucket, $fileName= 'TREATMENTPHOTO', $tempUrl=null)
    {
        //var_dump($s3Secret);

        $this->s3ResourcesBucket = $s3ResourceBucket;
        $this->s3Key = $s3Key;
        $this->s3Secret = $s3Secret;
        //var_dump($this->s3Secret);die;

        $this->scheme = $scheme;
        $this->region = $region;
        if(!(is_null($fileName)))
        {
            $this->fileName = $fileName;
        }

        if(!(is_null($tempUrl)))
        {
            $this->tempUrl = $tempUrl;
        }




        //$this->createS3Client($s3Key, $s3Secret, $scheme, $region);

    }

    private function createS3Client()
    {
        //var_dump($this->s3Secret);die;
         $s3Client = S3Client::factory(array(
         'key' => $this->s3Key,
         'secret' => $this->s3Secret,
         'region' => Region::AP_SOUTHEAST_1,
         'scheme' => 'https'
          ));

        return $s3Client;
     }


    private function uploadFile($uploadedUri, $localFile, $contentType='image/jpeg')
    {


               $client = $this->createS3Client();
           //var_dump($this->s3ResourcesBucket);die;
        //$buckets = $client->listBuckets();
        //var_dump("123");die;

        //$client->createBucket(array('bucket' => "Practo_Dev_Anshuman",
        //    'ACL' => ));

        //$buckets = $client->listBuckets();
        //var_dump($buckets->count());die;





           //var_dump($this->s3Client->listBuckets()); die;
           $response = $client->putObject(array(
               'Bucket'     => $this->s3ResourcesBucket,
               'Key'        => $uploadedUri,
               'SourceFile' => $localFile,
               'ACL'    => 'public-read'
           ));


       //var_dump($response);die;

        return $response;
    }

    public function add(FileBag $fileBag, $id)
    {
        $urls = new ArrayCollection();

       foreach( $fileBag->all() as  $file)
           {

               if($file instanceof UploadedFile)
               {

                   $uri = $this->processUploadedFile($file , $id);
                   //var_dump($uri); die;
                   $urls->add($uri);
               }

           }

        //var_dump($urls);die;

       return $urls;



    }

    /**
     * @param $uploadedFile  UploadedFile
     * @param $subId
     * @return mixed
     */
    protected function processUploadedFile($uploadedFile, $subId)
    {
        if (is_null($uploadedFile)) {
            return null;
        }

        if ($uploadedFile->getClientSize() == 0) {
            return null;
        }

       /* $mimeType = $uploadedFile->getClientMimeType();
        if ('application/x-base64-jpeg' === $mimeType) {
            $mimeType = 'image/jpeg';
        } if ('application/x-base64-png' === $mimeType) {
        $mimeType = 'image/png';
    }
        if ('jpeg' === $uploadedFile->guessExtension()) {
            $mimeType = 'image/jpeg';
        }*/


        $bits = '';
        $fp = @fopen('/dev/urandom', 'rb');
        if ($fp !== false) {
            $bits .= @fread($fp, 128);
            @fclose($fp);
        }
        $safeFileName = sha1($bits . time() . microtime()) . '.' . $uploadedFile->guessExtension();



        // Generate S3 Path
        $uploadsSubPath = $subId . '/' . $this->fileName;

        // Create temporary directory if not exists already
        $tmpDir = $this->tempUrl . $uploadsSubPath;
        if (!is_dir($tmpDir)) {
            $ret = mkdir($tmpDir, 0755, true);
            if (!$ret) {
                throw new \RuntimeException("Could not create target directory to move temporary file into.");
            }
        }

        // Copy uploaded file to temporary directory
        $uploadedFile->move($tmpDir, $safeFileName);

        $localFile = $tmpDir . DIRECTORY_SEPARATOR . $safeFileName;

        //mpDir . DIRECTORY_SEPARATOR . $safeFileName;
        if ('application/x-base64-jpeg' === $uploadedFile->getClientMimeType()) {
            $base64Image = file_get_contents($localFile);
            unlink($localFile);
            $safeFileName = sha1($bits . time() . microtime()) . '.jpg';
            $localFile = $tmpDir . DIRECTORY_SEPARATOR . $safeFileName;
            file_put_contents($localFile, base64_decode($base64Image));
        } else if ('application/x-base64-png' === $uploadedFile->getClientMimeType()) {
            $base64Image = file_get_contents($localFile);
            unlink($localFile);
            $safeFileName = sha1($bits . time() . microtime()) . '.png';
            $localFile = $tmpDir . DIRECTORY_SEPARATOR . $safeFileName;
            file_put_contents($localFile, base64_decode($base64Image));
        }
        $uploadedUri = $uploadsSubPath . DIRECTORY_SEPARATOR . $safeFileName;



       $response = $this->uploadFile($uploadedUri, $localFile);

        //var_dump($response); die;




        unlink($localFile);

        return $response->get('ObjectURL');
    }

    /**
     * Process Overlayed File
     *
     * @param string  $overlayBase64Encoded - Base 64 Encoded String
     * @param integer $practiceProfileId    - Practice Profile Id
     * @param file    $file                 - File
     *
     * @return ImageMetadata
     */
   /* protected function processOverlayedFile($overlayBase64Encoded, $practiceProfileId, $file)
    {
        $subId = $this->fileMapper->getSubscriptionId($practiceProfileId);
        $metadata = $file->getMetadata();

        if (!$metadata->getBaseImageUri()) {
            $metadata->setBaseImageUri($metadata->getUri());
        } else {
            $metadata->setBaseImageUri($metadata->getBaseImageUri());
        }

        $bits = '';
        $fp = @fopen('/dev/urandom', 'rb');
        if ($fp !== false) {
            $bits .= @fread($fp, 128);
            @fclose($fp);
        }
        $safeFileName = sha1($bits . time() . microtime()) . '.';

        // Generate S3 Path
        $uploadsSubPath = $subId . '/' . 'TREATMENTPHOTO';

        $tmpDir = '/tmp/practo-resources/' . $uploadsSubPath;
        if (!is_dir($tmpDir)) {
            $ret = mkdir($tmpDir, 0755, true);
            if (!$ret) {
                throw new \RuntimeException("Could not create target directory to move temporary file into.");
            }
        }

        $uploadedUri = $uploadsSubPath . DIRECTORY_SEPARATOR . $safeFileName;

        $tmpFilePath1 = $tmpDir.time().rand(1, 999);
        $tmpFilePath2 = $tmpDir.time().rand(1, 9999);
        file_put_contents($tmpFilePath1, base64_decode($overlayBase64Encoded));
        file_put_contents($tmpFilePath2,
            file_get_contents('http://s3-ap-southeast-1.amazonaws.com'.$this->getSignedFileURL($file, null, true)));

        $overlay = new \Imagick($tmpFilePath1);
        $image = new \Imagick($tmpFilePath2);

        $overlay->scaleImage($overlay->getImageWidth() * ($image->getImageWidth()/$overlay->getImageWidth()),
            $overlay->getImageHeight() * ($image->getImageHeight()/$overlay->getImageHeight()));

        $image->setImageColorspace($overlay->getImageColorspace());
        $image->compositeImage($overlay, \Imagick::COMPOSITE_DEFAULT, 0, 0);
        $finalOverlayedImage = $tmpDir.$safeFileName;
        $image->writeImage($finalOverlayedImage); //replace original background

        unlink($tmpFilePath1);
        unlink($tmpFilePath2);

        // Upload file to S3
        $s3Client = S3Client::factory(array(
            'key' => $this->s3Key,
            'secret' => $this->s3Secret,
            'region' => 'ap-southeast-1',
            'scheme' => 'https'
        ));
        $s3Client->putObject(array(
            'Bucket'     => $this->s3ResourcesBucket,
            'Key'        => $uploadedUri,
            'SourceFile' => $finalOverlayedImage
        ));

        $metadata->setWidth($image->getImageWidth());
        $metadata->setHeight($image->getImageHeight());
        $metadata->setUri($uploadedUri);

        unlink($finalOverlayedImage);

        return $metadata;
    }

    /**
     * Resize Image
     *
     * @param integer $originalImageId - Original Image Id
     * @param boolean $deleteCurrent   - Delete current resized images
     */
   /* protected function resizeImage($originalImageId, $deleteCurrent=false)
    {
        if (!$originalImageId) {
            throw new \Exception("Bad Original Image Id");
        }
        if ($deleteCurrent) {
            $fileMapper = $this->legacyMapperLoader->load('File');
            $fileMapper->deleteResizedImages($originalImageId);
        }

        $payload = array(
            'host'   => $this->practoDomain->getHost(),
            'fileId' => $originalImageId
        );

        $this->legacyQueue
            ->setQueueName(LegacyQueue::THUMBNAILS)
            ->sendMessage(json_encode($payload));
    }
*/
}