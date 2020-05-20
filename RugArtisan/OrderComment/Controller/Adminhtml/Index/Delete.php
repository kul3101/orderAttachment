<?php
/**
 * Delete file controller
 *
 * @author  Kuldip Chudasama
 * @package RugArtisan_OrderComment
 */

namespace RugArtisan\OrderComment\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Delete
 * @package RugArtisan\OrderComment\Controller\Adminhtml\Index
 */
class Delete extends Action
{
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $_mediaDirectory;

    /**
     * @var UploaderFactory
     */
    private $_fileUploaderFactory;

    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var File
     */
    private $_file;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * Delete constructor.
     * @param Context $context
     * @param JsonHelper $jsonHelper
     * @param JsonFactory $resultJsonFactory
     * @param Filesystem $filesystem
     * @param UploaderFactory $fileUploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param File $file
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        JsonHelper $jsonHelper,
        JsonFactory $resultJsonFactory,
        Filesystem $filesystem,
        UploaderFactory $fileUploaderFactory,
        StoreManagerInterface $storeManager,
        File $file
    ) {
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_storeManager = $storeManager;
        $this->_file = $file;
    }

    public function execute()
    {
        $_postData = $this->getRequest()->getPost();
        $message = "";
        $success = false;

        $mediaRootDir = $this->_mediaDirectory->getAbsolutePath();
        $_fileName = $mediaRootDir . 'leads/' . $_postData['filename'];
        if ($this->_file->isExists($_fileName)) {
            try {
                $this->_file->deleteFile($_fileName);
                $message = "File removed successfully.";
                $success = true;
            } catch (Exception $ex) {
                $message = $e->getMessage();
                $success = false;
            }
        } else {
            $message = "File not found.";
            $success = false;
        }

        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
                    'message' => $message,
                    'data' => '',
                    'success' => $success
        ]);
    }
}
