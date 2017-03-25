## Tools to transcoding/encoding audio or video, inspect and convert media formats.

## Установка

```bash
$ composer require arhitector/transcoder dev-master
```

## 1. Быстрый старт

Необходимо определить, с каким типом файлов предстоит работать.

`Arhitector\Transcoder\Audio` используется для работы с аудио-файлами.

`Arhitector\Transcoder\Video` используется для работы с видео-файлами.

`Arhitector\Transcoder\Frame` используется для работы с изображениями.

`Arhitector\Transcoder\Subtitle` используется для работы с субтитрами.

Конструктор в общем виде выглядит так

```php
public <...>::__construct(string $filePath, ServiceFactoryInterface $service = null)
```

`$filePath` - определяет путь до исходного файла. Вы не можете использовать удаленный источник или символические ссылки.

`$service` - не обязательный параметр, экземпляр сервиса. Если не передан, то будет использоваться `ServiceFactory`.

### 1.1. Примеры

Простые примеры

```php
use Arhitector\Transcoder\Audio;
use Arhitector\Transcoder\Video;
use Arhitector\Transcoder\Frame;
use Arhitector\Transcoder\Subtitle;

// аудио
$audio = new Audio('sample.mp3');

// видео
$video = new Video('sample.avi');

// изображения
$frame = new Frame('sample.jpg');

// субтитры
$subtitle = new Subtitle('sample.srt');
```

Вы можете использовать свою сервис-фабрику или изменить некоторые опции.

```php
$service = new \Arhitector\Transcoder\Service\ServiceFactory([
	'ffprobe.path'   => 'E:\devtools\bin\ffprobe.exe',
	'ffmpeg.path'    => 'E:\devtools\bin\ffmpeg.exe'
]);

// используем это
$video = new Video('sample.avi', $service);
```

## 2. Что можно настроить?

`ServiceFactory` поддерживает следующие опции:

- `ffmpeg.path` - путь до исполняемого файла `ffmpeg`

- `ffmpeg.threads` - FFMpeg-опция `threads`. По умолчанию `0`.

- `ffprobe.path` - путь до исполняемого файла `ffprobe`

- `timeout` - задаёт таймаут выполнения команды кодирования.

- `use_queue` - задача кодирования будет отправляться в очередь. Значение должно быть объектом,
 реализующим `SimpleQueue\QueueAdapterInterface`.

Вы можете использовать свою реализацию сервис-фабрики. Для этого необходимо реализовать в вашем объекте
 интерфейс `Arhitector\Transcoder\Service\ServiceFactoryInterface`.




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

### Аудио фильтры

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

Фильтр накладывает эффект затухания звука.

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

`available_audio_codecs`

*VideoFormatInterface* дополняет список *AudioFormatInterface*

`video_bitrate`

`passes`

`frame_rate`

