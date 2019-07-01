<?php
/**
 * This file is part of the arhitector/transcoder-ffmpeg library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2017-2019 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Transcoder\Tests\Traits;

use Arhitector\Transcoder\Traits\FilePathAwareTrait;

/**
 * Class FilePathAwareTraitTest.
 *
 * @package Arhitector\Transcoder\Tests\Traits
 */
class FilePathAwareTraitTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * @var FilePathAwareTrait
	 */
	protected $awareTrait;
	
	/**
	 * The set up method.
	 */
	public function setUp()
	{
		$this->awareTrait = $this->getObjectForTrait(FilePathAwareTrait::class);
	}
	
	/**
	 * Test on successful.
	 */
	public function testSuccessful()
	{
		$this->getReflection(__FILE__);
		$this->assertEquals($this->awareTrait->getFilePath(), __FILE__);
	}
	
	/**
	 * Test on failure.
	 *
	 * @param mixed $value
	 *
	 * @dataProvider dataProviderFailure
	 * @expectedException \InvalidArgumentException
	 */
	public function testFailure($value)
	{
		$this->getReflection($value);
	}
	
	/**
	 * Test on failure if the file path not found.
	 *
	 * @expectedException \Arhitector\Transcoder\Exception\TranscoderException
	 */
	public function testFailureNotFound()
	{
		$this->getReflection(__FILE__.mt_rand());
	}
	
	/**
	 * The data provider.
	 *
	 * @return array
	 */
	public function dataProviderFailure()
	{
		return [
			[new \stdClass],
			['https://example.com/file.ext']
		];
	}
	
	/**
	 * Get the ReflectionMethod instance.
	 *
	 * @param string $filePath
	 *
	 * @return \ReflectionMethod
	 */
	protected function getReflection($filePath)
	{
		$reflection = new \ReflectionMethod($this->awareTrait, 'setFilePath');
		$reflection->setAccessible(true);
		$reflection->invoke($this->awareTrait, $filePath);
		
		return $reflection;
	}
	
}
