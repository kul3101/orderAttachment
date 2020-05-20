<?php
/**
 * Comment Controller class
 *
 * @author  Kuldip Chudasama
 * @package RugArtisan_OrderComment
 */

namespace RugArtisan\OrderComment\Controller\Adminhtml\Index;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Controller\Adminhtml\Order\AddComment as AddCommentMain;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

/**
 * Class AddComment
 * @package RugArtisan\OrderComment\Controller\Adminhtml\Index
 */
class AddComment extends AddCommentMain
{
    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                $data = $this->getRequest()->getPost('history');
                if (empty($data['comment']) && $data['status'] == $order->getDataByKey('status')) {
                    throw new LocalizedException(
                        __('The comment is missing. Enter and try again.')
                    );
                }

                $notify = $data['is_customer_notified'] ?? false;
                $visible = $data['is_visible_on_front'] ?? false;

                if ($notify && !$this->_authorization->isAllowed(self::ADMIN_SALES_EMAIL_RESOURCE)) {
                    $notify = false;
                }

                $history = $order->addStatusHistoryComment($data['comment'], $data['status']);
                $history->setIsVisibleOnFront($visible);
                $history->setIsCustomerNotified($notify);
                if (isset($data['files-data']) && $data['files-data'] !== '') {
                    $history->setMedia($data['files-data']);
                }
                $history->save();

                $comment = trim(strip_tags($data['comment']));

                $order->save();
                /** @var OrderCommentSender $orderCommentSender */
                $orderCommentSender = $this->_objectManager
                    ->create(OrderCommentSender::class);

                $orderCommentSender->send($order, $notify, $comment);

                return $this->resultPageFactory->create();
            } catch (LocalizedException $e) {
                $response = ['error' => true, 'message' => $e->getMessage()];
            } catch (Exception $e) {
                $response = ['error' => true, 'message' => __('We cannot add order history.')];
            }
            if (is_array($response)) {
                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($response);
                return $resultJson;
            }
        }
        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}
