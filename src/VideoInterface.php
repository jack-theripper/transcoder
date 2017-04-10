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
namespace Arhitector\Transcoder;

/**
 * Interface VideoInterface.
 *
 * @package Arhitector\Transcoder
 */
interface VideoInterface extends FrameInterface, AudioInterface
{
	
	/**
	 * @var string 720x480 ntsc
	 */
	const SIZE_NTSC = '720x480';
	
	/**
	 * @var string 720x576 pal
	 */
	const SIZE_PAL = '720x576';
	
	/**
	 * @var string 352x240 qntsc
	 */
	const SIZE_QNTSC = '352x240';
	
	/**
	 * @var string 352x288 qpal
	 */
	const SIZE_QPAL = '352x288';
	
	/**
	 * @var string 640x480 sntsc
	 */
	const SIZE_SNTSC = '640x480';
	
	/**
	 * @var string 768x576 spal
	 */
	const SIZE_SPAL = '768x576';
	
	/**
	 * @var string 352x240 film
	 */
	const SIZE_FILM = '352x240';
	
	/**
	 * @var string 352x240 ntsc-film
	 */
	const SIZE_NTSC_FILM = '352x240';
	
	/**
	 * @var string 128x96 sqcif
	 */
	const SIZE_SQCIF = '128x96';
	
	/**
	 * @var string 176x144 qcif
	 */
	const SIZE_QCIF = '176x144';
	
	/**
	 * @var string 352x288 cif
	 */
	const SIZE_CIF = '352x288';
	
	/**
	 * @var string 704x576 cif
	 */
	const SIZE_4CIF = '704x576';
	
	/**
	 * @var string 1408x1152 16
	 */
	const SIZE_16CIF = '1408x1152';
	
	/**
	 * @var string 160x120 qqvga
	 */
	const SIZE_QQVGA = '160x120';
	
	/**
	 * @var string 320x240 qvga
	 */
	const SIZE_QVGA = '320x240';
	
	/**
	 * @var string 640x480 vga
	 */
	const SIZE_VGA = '640x480';
	
	/**
	 * @var string 800x600 svga
	 */
	const SIZE_SVGA = '800x600';
	
	/**
	 * @var string 1024x768 xga
	 */
	const SIZE_XGA = '1024x768';
	
	/**
	 * @var string 1600x1200 uxga
	 */
	const SIZE_UXGA = '1600x1200';
	
	/**
	 * @var string 2048x1536 qxga
	 */
	const SIZE_QXGA = '2048x1536';
	
	/**
	 * @var string 1280x1024 sxga
	 */
	const SIZE_SXGA = '1280x1024';
	
	/**
	 * @var string 2560x2048 qsxga
	 */
	const SIZE_QSXGA = '2560x2048';
	
	/**
	 * @var string 5120x4096 hsxga
	 */
	const SIZE_HSXGA = '5120x4096';
	
	/**
	 * @var string 852x480 wvga
	 */
	const SIZE_WVGA = '852x480';
	
	/**
	 * @var string 1366x768 wxga
	 */
	const SIZE_WXGA = '1366x768';
	
	/**
	 * @var string 1600x1024 wsxga
	 */
	const SIZE_WSXGA = '1600x1024';
	
	/**
	 * @var string 1920x1200 wuxga
	 */
	const SIZE_WUXGA = '1920x1200';
	
	/**
	 * @var string 2560x1600 woxga
	 */
	const SIZE_WOXGA = '2560x1600';
	
	/**
	 * @var string 3200x2048 wqsxga
	 */
	const SIZE_WQSXGA = '3200x2048';
	
	/**
	 * @var string 3840x2400 wquxga
	 */
	const SIZE_WQUXGA = '3840x2400';
	
	/**
	 * @var string 6400x4096 whsxga
	 */
	const SIZE_WHSXGA = '6400x4096';
	
	/**
	 * @var string 7680x4800 whuxga
	 */
	const SIZE_WHUXGA = '7680x4800';
	
	/**
	 * @var string 320x200 cga
	 */
	const SIZE_CGA = '320x200';
	
	/**
	 * @var string 640x350 ega
	 */
	const SIZE_EGA = '640x350';
	
	/**
	 * @var string 852x480 hd480
	 */
	const SIZE_HD480 = '852x480';
	
	/**
	 * @var string 1280x720 hd720
	 */
	const SIZE_HD720 = '1280x720';
	
	/**
	 * @var string 1920x1080 hd1080
	 */
	const SIZE_HD1080 = '1920x1080';
	
	/**
	 * @var string 2048x1080 2k
	 */
	const SIZE_2K = '2048x1080';
	
	/**
	 * @var string 1998x1080 2kflat
	 */
	const SIZE_2KFLAT = '1998x1080';
	
	/**
	 * @var string 2048x858 2kscope
	 */
	const SIZE_2KSCOPE = '2048x858';
	
	/**
	 * @var string 4096x2160 4k
	 */
	const SIZE_4K = '4096x2160';
	
	/**
	 * @var string 3996x2160 4kflat
	 */
	const SIZE_4KFLAT = '3996x2160';
	
	/**
	 * @var string 4096x1716 4kscope
	 */
	const SIZE_4KSCOPE = '4096x1716';
	
	/**
	 * @var string 640x360 nhd
	 */
	const SIZE_NHD = '640x360';
	
	/**
	 * @var string 240x160 hqvga
	 */
	const SIZE_HQVGA = '240x160';
	
	/**
	 * @var string 400x240 wqvga
	 */
	const SIZE_WQVGA = '400x240';
	
	/**
	 * @var string 432x240 fwqvga
	 */
	const SIZE_FWQVGA = '432x240';
	
	/**
	 * @var string 480x320 hvga
	 */
	const SIZE_HVGA = '480x320';
	
	/**
	 * @var string 960x540 qhd
	 */
	const SIZE_QHD = '960x540';
	
	/**
	 * @var string 2048x1080 2kdci
	 */
	const SIZE_2KDCI = '2048x1080';
	
	/**
	 * @var string 4096x2160 4kdci
	 */
	const SIZE_4KDCI = '4096x2160';
	
	/**
	 * @var string 3840x2160 uhd2160
	 */
	const SIZE_UHD2160 = '3840x2160';
	
	/**
	 * @var string 7680x4320 uhd4320
	 */
	const SIZE_UHD4320 = '7680x4320';
	
	/**
	 * Gets the bitrate value.
	 *
	 * @return int
	 */
	public function getKiloBitrate();
	
	/**
	 * Get frame rate value.
	 *
	 * @return float
	 */
	public function getFrameRate();
	
}
