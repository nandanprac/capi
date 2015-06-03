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


        $this->s3ResourcesBucket = $s3ResourceBucket;
        $this->s3Key = $s3Key;
        $this->s3Secret = $s3Secret;

        $this->scheme = $scheme;
        $this->region = $region;
        if(!(is_null($fileName))) {
            $this->fileName = $fileName;
        }

        if(!(is_null($tempUrl))) {
            $this->tempUrl = $tempUrl;
        }




    }

    private function createS3Client()
    {
         $s3Client = S3Client::factory(
             array(
             'key' => $this->s3Key,
             'secret' => $this->s3Secret,
             'region' => Region::AP_SOUTHEAST_1,
             'scheme' => 'https'
             )
         );

         return $s3Client;
    }


    private function uploadFile($uploadedUri, $localFile, $contentType='image/jpeg')
    {


               $client = $this->createS3Client();

           $response = $client->putObject(
               array(
               'Bucket'     => $this->s3ResourcesBucket,
               'Key'        => $uploadedUri,
               'SourceFile' => $localFile,
               'ACL'    => 'public-read'
               )
           );



           return $response;
    }

    public function add(FileBag $fileBag, $id)
    {
        $urls = new ArrayCollection();

        foreach( $fileBag->all() as  $file)
           {

            if($file instanceof UploadedFile) {

                $uri = $this->processUploadedFile($file, $id);

                $urls->add($uri);
            }

        }



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






        unlink($localFile);

        return $response->get('ObjectURL');
    }

}