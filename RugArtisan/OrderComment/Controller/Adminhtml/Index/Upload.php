<?php
/**
 * Upload Controller class
 *
 * @author  Kuldip Chudasama
 * @package RugArtisan_OrderComment
 */

namespace RugArtisan\OrderComment\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Upload
 * @package RugArtisan\OrderComment\Controller\Adminhtml\Index
 */
class Upload extends Action
{
    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $_mediaDirectory;

    /**
     * @var UploaderFactory
     */
    private $_fileUploaderFactory;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * Upload constructor.
     *
     * @param Context $context
     * @param JsonHelper $jsonHelper
     * @param JsonFactory $resultJsonFactory
     * @param Filesystem $filesystem
     * @param UploaderFactory $fileUploaderFactory
     * @param StoreManagerInterface $storeManager
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        JsonHelper $jsonHelper,
        JsonFactory $resultJsonFactory,
        Filesystem $filesystem,
        UploaderFactory $fileUploaderFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_storeManager = $storeManager;
    }

    public function execute()
    {
        $_postData = $this->getRequest()->getPost();
        $message = "";
        $newFileName = "";
        $error = false;
        $data = [];

        try {
            $target = $this->_mediaDirectory->getAbsolutePath('leads/');
            //attachment is the input file name posted from your form
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'attachment']);

            $_fileType = $uploader->getFileExtension();
            $newFileName = uniqid() . '.' . $_fileType;

            /** Allowed extension types */
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            /** rename file name if already exists */
            $uploader->setAllowRenameFiles(true);

            $result = $uploader->save($target, $newFileName); //Use this if you want to change your file name
            if ($result['file']) {
                $_mediaUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
                $_src = $_mediaUrl . 'leads/' . $newFileName;
                $error = false;
                $message = "File has been successfully uploaded";

                $html = '<div class="image item base-image" data-role="image" id="' . uniqid() . '">
                            <div class="product-image-wrapper">
                                <img class="product-image" data-role="image-element" src="' . $_src . '" alt="">
                                <div class="actions">
                                    <button type="button" class="action-remove" data-role="delete-button" data-image="' . $newFileName . '" title="Delete image"><span>Delete image</span></button>
                                </div>
                                <div class="image-fade"><span>Hidden</span></div>
                            </div>
                            <div class="item-description">
                                <div class="item-title" data-role="img-title"></div>
                                <div class="item-size">
                                    <a href="' . $_mediaUrl . 'leads/' . $newFileName . '" target="_blank"><span data-role="image-dimens">' . $newFileName . '</span></a>
                                </div>
                            </div>
                        </div>';

                $data = ['filename' => $newFileName, 'path' => $_mediaUrl . 'leads/' . $newFileName, 'fileType' => $_fileType, 'html' => $html];
                $success = true;
            }
        } catch (Exception $e) {
            $error = true;
            $success = false;
            $message = $e->getMessage();
        }
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData([
            'message' => $message,
            'file' => $newFileName,
            'data' => $data,
            'success' => $success,
            'error' => $error
        ]);
    }
}
