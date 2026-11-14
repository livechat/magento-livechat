<?php
declare(strict_types=1);

namespace LiveChat\LiveChat\Test\Unit\Controller\GetCart;

use LiveChat\LiveChat\Controller\GetCart\Index;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    public function testGetLastOrderDetailsMapsCreatedAtAndUpdatedAtToTheirOwnColumns(): void
    {
        $customerId = 42;

        $customer = $this->createStub(Customer::class);
        $customer->method('getId')->willReturn($customerId);

        $customerSession = $this->createStub(CustomerSession::class);
        $customerSession->method('getCustomer')->willReturn($customer);

        $order = $this->createStub(Order::class);
        $order->method('getData')->willReturnCallback(
            function (string $key) {
                $data = [
                    'created_at' => '2026-01-01 10:00:00',
                    'updated_at' => '2026-02-01 11:00:00',
                    'status' => 'complete',
                    'state' => 'complete',
                    'grand_total' => 123.45,
                    'order_currency_code' => 'USD',
                ];

                return $data[$key] ?? null;
            }
        );

        $orderCollection = $this->createStub(OrderCollection::class);
        $orderCollection->method('addFieldToFilter')->willReturnSelf();
        $orderCollection->method('addOrder')->willReturnSelf();
        $orderCollection->method('fetchItem')->willReturn($order);

        $orderCollectionFactory = $this->createStub(OrderCollectionFactory::class);
        $orderCollectionFactory->method('create')->willReturn($orderCollection);

        $controller = new Index(
            $this->createStub(Context::class),
            $this->createStub(Cart::class),
            $customerSession,
            $orderCollectionFactory,
            $this->createStub(ScopeConfigInterface::class)
        );

        $this->assertSame(
            'Created at: 2026-01-01 10:00:00, updated at: 2026-02-01 11:00:00, '
                . 'status: complete, state: complete, grand total: 123.45 USD',
            $controller->getLastOrderDetails()
        );
    }
}
