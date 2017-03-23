## Tools to transcoding/encoding audio or video, inspect and convert media formats.

## Установка

```bash
$ composer require arhitector/jumper dev-master
```

## Использование

```php
// Это список всех поддерживаемых опций.
// Конечно эти опции можно опустить если ffmpeg доступен из 'Path'.
$options = [
	'ffprobe.path'   => 'E:\devtools\bin\ffprobe.exe',
	'ffmpeg.path'    => 'E:\devtools\bin\ffmpeg.exe',
	'ffmpeg.threads' => 2,
	'timeout'        => 30
];

$factory = new \Arhitector\Transcoder\Service\ServiceFactory($options);
```

### Извлечение информации из видеофайла, аудио файла и т.д.

```php
use Arhitector\Transcoder\Video;
use Arhitector\Transcoder\Audio;

$video = new Video('sample.avi');

var_dump($video->getWidth(), $video->getHeight());

$audio = new Audio(__DIR__.'/audio.mp3', $factory);

var_dump($audio->getAudioChannels());
var_dump($audio->getFormat()->getTags());
```

### Извлечение звука из видеофайла с последующим сохранением в формате MP3

Этот простой пример показывает лишь принцип, таким же способом можно сохранить субтитры или обложку из Mp3-файла и т.д.

```php
use Arhitector\Transcoder\Video;
use Arhitector\Transcoder\Stream\AudioStreamInterface;
use Arhitector\Transcoder\Format\Mp3;

$video = new Video('sample.mp4');

foreach ($video->getStreams() as $stream)
{
	// тут выбираем только аудио канал
	if ($stream instanceof AudioStreamInterface)
	{
		$stream->save(new Mp3(), __DIR__.'/only-audio.mp3');
		
		break; // видео может иметь несколько аудио потоков
	}
}
```

### Преобразование из одного формата в любой другой

```php
use Arhitector\Transcoder\Audio;
use Arhitector\Transcoder\Format\Mp3;

$audio = new Audio('audio-file.wav');
$audio->save(new Mp3(), 'audio-file.mp3');

use Arhitector\Transcoder\Video;
use Arhitector\Transcoder\Format\VideoFormat;

$video = new Video('video-file.avi');
$video->save(new VideoFormat('aac', 'h264'), 'video-file.mp4');
```

### Добавление/Изменение метаинформации

```php
use Arhitector\Transcoder\Audio;

$audio = new Audio('file.mp3');

$format = $audio->getFormat();
$format['artist'] = 'Новый артист';

$auiod->save($format, 'new-file.mp3');
```

## Фильтры

### Айдио фильтры

- Фильтр **Volume**

Фильтр изменяет громкость аудио потока.

```php
use \Arhitector\Jumper\Filter\Volume;
```

Пример показывает как уменьшить громкость аудио.

```php
$filter = new Volume(0.5);
$filter = new Volume(1/2);
$filter = new Volume('6.0206dB');
```

Increase input audio power by 6 decibels using fixed-point precision.

```php
$filter = new Volume('6dB', Volume::PRECISION_FIXED);
```

- Фильтр **Fade**

Фильтр накладывает еффект затухания звука.

```php
use \Arhitector\Jumper\Filter\Fade;
```

## Опции форматов

*FormatInterface* определяет 

`duration`

`extensions`

`metadata`

*FrameFormatInterface* дополняет список *FormatInterface*

`video_codec`

`width`

`height`

`available_video_codecs`

*AudioFormatInterface* дополняет список *FrameFormatInterface*

`channels`

`audio_codec`

`audio_bitrate`

`frequency`

`available_audio_coecs`

*VideoFormatInterface* дополняет список *AudioFormatInterface*

`video_bitrate`

`passes`

`frame_rate`

