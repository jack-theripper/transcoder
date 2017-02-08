<?php
/**
 * This file is part of the arhitector/transcoder library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2017 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Transcoder\Tests\Stream;

use Arhitector\Transcoder\Codec;
use Arhitector\Transcoder\Stream\StreamTrait;
use Arhitector\Transcoder\TranscodeInterface;
use PHPUnit_Framework_Error;

/**
 * Class StreamTraitTest.
 *
 * @package Arhitector\Transcoder\Tests\Stream
 */
class StreamTraitTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * @var StreamTrait
	 */
	protected $awareTrait;
	
	public function setUp()
	{
		$this->awareTrait = $this->getObjectForTrait(StreamTrait::class);
	}
	
	public function testConstructor()
	{
		$reflection = new \ReflectionMethod(get_class($this->awareTrait), '__construct');
		$this->assertTrue($reflection->isPrivate());
	}
	
	/**
	 * @dataProvider dataConstructSuccess
	 *
	 * @param mixed $value
	 */
	public function testConstructSuccess($value)
	{
		$reflection = new \ReflectionMethod(get_class($this->awareTrait), '__construct');
		$reflection->setAccessible(true);
		$reflection->invoke($this->awareTrait, $value);
	}
	
	/**
	 * @dataProvider dataConstructFailure
	 *
	 * @param mixed $value
	 */
	public function testConstructFailure($value)
	{
		$reflection = new \ReflectionMethod(get_class($this->awareTrait), '__construct');
		$reflection->setAccessible(true);
		$this->expectException(get_class(new PHPUnit_Framework_Error('', 0, '', 1)));
		$reflection->invoke($this->awareTrait, $value);
	}
	
	public function testSetterGetterCodecSuccessful()
	{
		$codec = new Codec('codec string', 'codec name');
		$this->awareTrait->setCodec($codec);
		
		$this->assertEquals($codec, $this->awareTrait->getCodec());
	}
	
	public function testSetterGetterCodecFailure()
	{
		$this->expectException(get_class(new PHPUnit_Framework_Error('', 0, '', 1)));
		$this->awareTrait->setCodec('codec string');
	}
	
	public function dataConstructSuccess()
	{
		$transcoderMock = $this->getMockBuilder(TranscodeInterface::class)
			->getMock();
		
		$transcoderMock->expects($this->any())
			->method('getFilePath')
			->willReturn(__FILE__);
		
		return [
			[$transcoderMock]
		];
	}
	
	public function dataConstructFailure()
	{
		return [
			['string'],
		    [0.123456789],
		    [new \stdClass]
		];
	}
	
	public function dataSettersGetters()
	{
		return [
			
		];
	}
	
}
