<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCheckout\Test\Unit\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\AdvancedCheckout\Helper\Data
     */
    protected $checkoutDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $factoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Filesystem
     */
    protected $filesystemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $writeDirectoryMock;

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject|\Magento\MediaStorage\Model\File\Uploader
     */
    protected $uploaderMock;

    /**
     * @var \Magento\AdvancedCheckout\Model\Import
     */
    protected $import;

    protected function setUp()
    {
        $this->checkoutDataMock = $this->getMock('Magento\AdvancedCheckout\Helper\Data', [], [], '', false);
        $this->factoryMock = $this->getMock(
            'Magento\MediaStorage\Model\File\UploaderFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->filesystemMock = $this->getMock('Magento\Framework\Filesystem', [], [], '', false);

        $this->writeDirectoryMock = $this->getMock('Magento\Framework\Filesystem\Directory\Write', [], [], '', false);
        $this->uploaderMock = $this->getMock('Magento\MediaStorage\Model\File\Uploader', [], [], '', false);
        $this->import = new \Magento\AdvancedCheckout\Model\Import(
            $this->checkoutDataMock,
            $this->factoryMock,
            $this->filesystemMock
        );

    }

    public function testUploadFile()
    {
        $this->prepareUploadFileData();
        $this->import->uploadFile();
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage This file needs to be in .csv format.
     */
    public function testUploadFileWhenExtensionIsNotAllowed()
    {
        $allowedExtension = 'csv';
        $this->factoryMock
            ->expects($this->once())
            ->method('create')
            ->with(['fileId' => 'sku_file'])
            ->willReturn($this->uploaderMock);
        $this->uploaderMock->expects($this->once())->method('setAllowedExtensions')->with(['csv']);
        $this->uploaderMock->expects($this->once())->method('skipDbProcessing')->with(true);
        $this->uploaderMock->expects($this->once())->method('getFileExtension')->willReturn($allowedExtension);
        $this->uploaderMock
            ->expects($this->once())
            ->method('checkAllowedExtension')
            ->with($allowedExtension)
            ->willReturn(false);
        $this->writeDirectoryMock
            ->expects($this->never())
            ->method('getAbsolutePath');
        $this->import->uploadFile();
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testUploadFileWhenImposibleSaveAbsolutePath()
    {
        $this->filesystemMock
            ->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::VAR_DIR)
            ->willReturn($this->writeDirectoryMock);
        $allowedExtension = 'csv';
        $absolutePath = 'path/path2';
        $phraseMock = $this->getMock('\Magento\Framework\Phrase', [], [], '', false);
        $this->factoryMock
            ->expects($this->once())
            ->method('create')
            ->with(['fileId' => 'sku_file'])
            ->willReturn($this->uploaderMock);
        $this->uploaderMock->expects($this->once())->method('setAllowedExtensions')->with(['csv']);
        $this->uploaderMock->expects($this->once())->method('skipDbProcessing')->with(true);
        $this->uploaderMock->expects($this->once())->method('getFileExtension')->willReturn($allowedExtension);
        $this->uploaderMock
            ->expects($this->once())
            ->method('checkAllowedExtension')
            ->with($allowedExtension)
            ->willReturn(true);
        $this->writeDirectoryMock
            ->expects($this->once())
            ->method('getAbsolutePath')
            ->with('import_sku/')
            ->willReturn($absolutePath);
        $this->uploaderMock
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new \Exception());
        $this->writeDirectoryMock
            ->expects($this->never())
            ->method('getRelativePath');
        $this->checkoutDataMock->expects($this->once())->method('getFileGeneralErrorText')->willReturn($phraseMock);
        $this->import->uploadFile();
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testGetDataFromCsvWhenFileNotExist()
    {
        $phraseMock = $this->getMock('\Magento\Framework\Phrase', [], [], '', false);
        $this->checkoutDataMock->expects($this->once())->method('getFileGeneralErrorText')->willReturn($phraseMock);
        $this->import->getDataFromCsv();
    }

    public function testGetDataFromCsv()
    {
        $colNames = ['sku', 'qty'];
        $currentRow = [
            0 => 'ProductSku',
            1 => 3
        ];
        $expectedCsvData = [
            ['qty' => 3,
            'sku' => 'ProductSku'
            ]
        ];
        $fileHandlerMock = $this->getMock('Magento\Framework\Filesystem\File\WriteInterface');
        $this->writeDirectoryMock
            ->expects($this->once())
            ->method('isExist')
            ->with('file_name.csv')
            ->willReturn(true);
        $this->writeDirectoryMock
            ->expects($this->once())
            ->method('openFile')
            ->with('file_name.csv', 'r')
            ->willReturn($fileHandlerMock);
        $fileHandlerMock->expects($this->at(0))->method('readCsv')->willReturn($colNames);
        $fileHandlerMock->expects($this->at(1))->method('readCsv')->willReturn($currentRow);
        $fileHandlerMock->expects($this->at(2))->method('readCsv')->willReturn(false);
        $fileHandlerMock->expects($this->once())->method('close');
        $this->prepareUploadFileData();
        $this->import->uploadFile();
        $this->assertEquals($expectedCsvData, $this->import->getDataFromCsv());
    }


    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage The file is corrupt.
     */
    public function testGetDataFromCsvFromInvalidFile()
    {
        $colNames = ['one', 'qty'];
        $fileHandlerMock = $this->getMock('Magento\Framework\Filesystem\File\WriteInterface');
        $this->writeDirectoryMock
            ->expects($this->once())
            ->method('isExist')
            ->with('file_name.csv')
            ->willReturn(true);
        $this->writeDirectoryMock
            ->expects($this->once())
            ->method('openFile')
            ->with('file_name.csv', 'r')
            ->willReturn($fileHandlerMock);
        $phraseMock = $this->getMock('\Magento\Framework\Phrase', [], [], '', false);
        $this->checkoutDataMock->expects($this->once())->method('getSkuEmptyDataMessageText')->willReturn($phraseMock);
        $fileHandlerMock->expects($this->at(0))->method('readCsv')->willReturn($colNames);
        $this->prepareUploadFileData();
        $this->import->uploadFile();
        $this->import->getDataFromCsv();
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage The file is corrupt.
     */
    public function testGetDataFromCsvWhenFileCorrupt()
    {
        $this->writeDirectoryMock
            ->expects($this->once())
            ->method('isExist')
            ->with('file_name.csv')
            ->willReturn(true);
        $this->writeDirectoryMock
            ->expects($this->once())
            ->method('openFile')
            ->with('file_name.csv', 'r')
            ->willThrowException(new \Exception());
        $this->prepareUploadFileData();
        $this->import->uploadFile();
        $this->import->getDataFromCsv();
    }

    public function testDestruct()
    {
        $this->writeDirectoryMock->expects($this->once())->method('delete')->with('file_name.csv');
        $this->prepareUploadFileData();
        $this->import->uploadFile();
        $this->import->destruct();
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testGetRowsWhenFileNotExist()
    {
        $phraseMock = $this->getMock('\Magento\Framework\Phrase', [], [], '', false);
        $this->checkoutDataMock->expects($this->once())->method('getFileGeneralErrorText')->willReturn($phraseMock);
        $this->prepareUploadFileData();
        $this->import->uploadFile();
        $this->import->getRows();
    }

    protected function prepareUploadFileData()
    {
        $this->filesystemMock
            ->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::VAR_DIR)
            ->willReturn($this->writeDirectoryMock);
        $allowedExtension = 'csv';
        $absolutePath = 'path/path2';
        $result = [
            'path' => $absolutePath,
            'file' => 'file_name.csv'
        ];
        $this->factoryMock
            ->expects($this->once())
            ->method('create')
            ->with(['fileId' => 'sku_file'])
            ->willReturn($this->uploaderMock);
        $this->uploaderMock->expects($this->once())->method('setAllowedExtensions')->with(['csv']);
        $this->uploaderMock->expects($this->once())->method('skipDbProcessing')->with(true);
        $this->uploaderMock->expects($this->once())->method('getFileExtension')->willReturn($allowedExtension);
        $this->uploaderMock
            ->expects($this->once())
            ->method('checkAllowedExtension')
            ->with($allowedExtension)
            ->willReturn(true);
        $this->writeDirectoryMock
            ->expects($this->once())
            ->method('getAbsolutePath')
            ->with('import_sku/')
            ->willReturn($absolutePath);
        $this->uploaderMock
            ->expects($this->once())
            ->method('save')
            ->with($absolutePath)
            ->willReturn($result);
        $this->writeDirectoryMock
            ->expects($this->once())
            ->method('getRelativePath')
            ->with($result['path'] . $result['file'])
            ->willReturn('file_name.csv');
    }
}
