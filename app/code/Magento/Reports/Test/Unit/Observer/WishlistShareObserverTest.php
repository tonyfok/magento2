<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Reports\Test\Unit\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Reports\Model\Event;
use Magento\Reports\Model\ReportStatus;
use Magento\Reports\Observer\EventSaver;
use Magento\Reports\Observer\WishlistShareObserver;
use Magento\Wishlist\Model\Wishlist;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for \Magento\Reports\Observer\WishlistShareObserver
 */
class WishlistShareObserverTest extends TestCase
{
    /**
     * @var Observer|MockObject
     */
    private $eventObserverMock;

    /**
     * @var EventSaver|MockObject
     */
    private $eventSaverMock;

    /**
     * @var ReportStatus|MockObject
     */
    private $reportStatusMock;

    /**
     * @var WishlistShareObserver
     */
    private $observer;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->eventObserverMock = $this->createMock(Observer::class);
        $this->eventSaverMock = $this->createMock(EventSaver::class);
        $this->reportStatusMock = $this->createMock(ReportStatus::class);

        $this->observer = (new ObjectManagerHelper($this))->getObject(
            WishlistShareObserver::class,
            ['eventSaver' => $this->eventSaverMock, 'reportStatus' => $this->reportStatusMock]
        );
    }

    /**
     * Test case when report is disabled in config.
     */
    public function testExecuteWhenReportIsDisabled()
    {
        $this->reportStatusMock->expects($this->once())
            ->method('isReportEnabled')
            ->with(Event::EVENT_WISHLIST_SHARE)
            ->willReturn(false);
        $this->eventSaverMock->expects($this->never())->method('save');
        $this->observer->execute($this->eventObserverMock);
    }

    /**
     * Test case when report is enabled in config.
     */
    public function testExecuteWhenReportIsEnabled()
    {
        $this->reportStatusMock->expects($this->once())
            ->method('isReportEnabled')
            ->with(Event::EVENT_WISHLIST_SHARE)
            ->willReturn(true);

        $eventMock = $this->getMockBuilder(Event::class)
            ->addMethods(['getWishlist'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())
            ->method('getWishlist')
            ->willReturn($this->createMock(Wishlist::class));
        $this->eventObserverMock->expects($this->once())->method('getEvent')->willReturn($eventMock);

        $this->eventSaverMock->expects($this->once())->method('save');
        $this->observer->execute($this->eventObserverMock);
    }
}
