
# Transcoder

[![Latest Version](https://img.shields.io/github/release/jack-theripper/transcoder.svg?style=flat-square)](https://github.com/jack-theripper/transcoder/releases)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Build Status](https://img.shields.io/travis/jack-theripper/transcoder/master.svg?style=flat-square)](https://travis-ci.org/jack-theripper/transcoder)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/jack-theripper/transcoder.svg?style=flat-square)](https://scrutinizer-ci.com/g/jack-theripper/transcoder/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/jack-theripper/transcoder.svg?style=flat-square)](https://scrutinizer-ci.com/g/jack-theripper/transcoder)
[![Total Downloads](https://img.shields.io/packagist/dt/arhitector/transcoder.svg?style=flat-square)](https://packagist.org/packages/arhitector/transcoder)

#### Tools to transcoding/encoding audio or video, inspect and convert media formats.

Инструмент для кодирования, конвертации, и получения метаинформации для аудио и видео.

## Требования

* PHP 5.6 или новее
* Установленный [FFMpeg](http://ffmpeg.org)

## Установка

Поддерживается установка с помощью менеджера пакетов [Composer](http://getcomposer.org/).

```bash
$ composer require arhitector/transcoder dev-master
```

Вы также можете внести зависимость в уже существующий файл `composer.json` самостоятельно.

```json
{
	"require": {
		"arhitector/transcoder": "dev-master"
	}
}
```

## Оказать содействие

Нашли ошибку или есть идея для новой функции? Пожалуйста, [откройте новый вопрос](https://github.com/jack-theripper/transcoder/issues).

## С чего начать

В зависимости от контента, вы можете использовать `Audio` для работы с аудио-файлами, `Frame` для изображений, а `Video` и `Subtitle` соответственно для видео-файлов и субтитров.

Конструктор в общем виде выглядит так:

```php
public < ... >::__construct(string $filePath, ServiceFactoryInterface $service = null)
```

`$filePath` - строка, путь до исходного файла.

> Вы не можете использовать удаленный источник или символические ссылки.

`$service` - параметр не обязателен. Экземпляр сервиса. По умолчанию   `ServiceFactory`.

**Пример №1**

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

**Пример №2**

Вы можете использовать свою сервис-фабрику или изменить некоторые опции.

```php
use Arhitector\Transcoder\Service\ServiceFactory;

$service = new ServiceFactory([
	'ffprobe.path'   => 'E:\devtools\bin\ffprobe.exe',
	'ffmpeg.path'    => 'E:\devtools\bin\ffmpeg.exe'
]);

// используем это
$video = new Video('sample.avi', $service);
```

### События

Экземпляр формата позволяет регистрировать обработчики событий. Читать подробнее [League\Event](http://event.thephpleague.com/2.0/).

**Пример №1**

Добавим обработчик на событие.

```php
$format = new VideoFormat();
$format->addListener('*', function ($event) {
	// обработчик сработает на любое событие
});
```

#### Поддерживаемые события

- `before` выполняется перед началом кодирования. Вы можете отменить процесс вызвав `$event->stopPropagation()`.

**Пример №2**

Операция будет отменена и вызов последующих событий НЕ произойдёт.

```php
$format = new AudioFormat();
$format->addListener('before', function ($event) {
	$event->stopPropagation();
});
```

- `before.pass` событие вызывается перед каждым проходом при многопроходном кодировании. Будет вызвано минимум 1 раз.

- `success` сработает в случае если операция успешна.

- `progress` срабатывает в ходе выполнения операции.

**Пример №3**

```php
$format = new VideoFormat();
$format->addListener('progress', function ($event) {
	/* @var Arhitector/Transcoder/Event/EventProgress $event */
	var_dump($event->getPercent());
});
```

- `failure` если что-то пошло не так.

- `after` обработчик будет вызван когда операция завершится, не зависимо от того была ли операция завершена успешно или нет.

- `after.pass` срабатывает после завершения прохода при многопроходном кодировании. Будет вызвано минимум 1 раз.

### Поддержка очередей

Вместо прямого транскодирования вы можете отправлять задачи в очередь, например, на сервер очередей. Такой функционал
 доступен прямо из коробки. Вы можете использовать опцию `ServiceFactoryInterface::OPTION_USE_QUEUE` при создании сервис-фабрики.
Читать подробнее [SimpleQueue](https://github.com/fguillot/simple-queue).

**Пример**

```php
$adapter = new SimpleQueue\Adapter\MemoryQueueAdapter();
$queue = new SimpleQueue\Queue($queue);

$service = new Arhitector\Transcoder\Service\ServiceFactory([
    Arhitector\Transcoder\Service\ServiceFactory::OPTION_USE_QUEUE => $queue
]);

$audio = new Arhitector\Transcoder\Audio('sample.mp3', $service);

// задача будет отправлена в очередь `$queue`
$audio->save($audio->getFormat(), 'new-sample.mp3');

var_dump($queue->pull()); // запросить задачу из очереди
```

## Что можно настроить? Поддерживаемые опции

###  Опции сервис-фабрики

Вы можете использовать свою реализацию сервис-фабрики. Для этого необходимо реализовать интерфейс `Arhitector\Transcoder\Service\ServiceFactoryInterface`.
 
`ServiceFactory` поддерживает следующие опции:

1. `ffmpeg.path` - путь до исполняемого файла ffmpeg

1. `ffmpeg.threads` - FFMpeg-опция threads. По умолчанию `0`.

1. `ffprobe.path` - путь до исполняемого файла ffprobe

1. `timeout` - задаёт таймаут выполнения команды кодирования.

1. `use_queue` - Отправляет задачу в очередь. Значение должно быть объектом, реализующим `SimpleQueue\QueueAdapterInterface`.

## Примеры 

### Извлечение информации из видео файла, аудио файла и т.д.

```php
use Arhitector\Transcoder\Video;
use Arhitector\Transcoder\Audio;

$video = new Video('sample.avi');

var_dump($video->getWidth(), $video->getHeight());

$audio = new Audio(__DIR__.'/audio.mp3', $factory);

var_dump($audio->getAudioChannels());
var_dump($audio->getFormat()->getTags());
```

### Извлечение звука из видео файла с последующим сохранением в формате MP3

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

### Добавление/Изменение мета-информации

```php
use Arhitector\Transcoder\Audio;

$audio = new Audio('file.mp3');

$format = $audio->getFormat();
$format['artist'] = 'Новый артист';

$auiod->save($format, 'new-file.mp3');
```

### Как добавить/изменить обложку MP3-файла?

```php
use Arhitector\Transcoder\Audio;
use Arhitector\Transcoder\Frame;

$audio = new Audio(__DIR__.'/sample.mp3');
$streams = $audio->getStreams();

$new_cover = (new Frame(__DIR__.'/sample.jpg'))
    ->getStreams()
    ->getFirst();

// индекс `0` - аудио-дорожка, `1` - обложка.
$streams[1] = $new_cover;

$audio->save($audio->getFormat(), 'sample-with-new-cover.mp3');
```

## ООП-обёртки над форматами

Такие обёртки (например, Mp3 или Jpeg и т.д.) созданы для удобства. 

### Изображения

- Png, Jpeg, Ppm, Bmp, Gif

### Аудио-форматы

- Aac, Mp3, Oga, Flac

### Видео-форматы

- Flv, Mkv

## Фильтры

Фильтры используются для изменения исходного медиа контента. Могут иметь один или несколько входов и выходов. 
Фильтры могут быть организованы в цепочки фильтров для изоляции некоторый фильтров друг от друга.

```php
public < ... >::addFilter(FilterInterface $filter, $priority = 0);
```

`$filter` экземпляр фильтра.

`$priority` вы можете задать приоритет для фильтров. На основе приоритета определяется порядок использования фильтра. По умолчанию `0`.

**Пример №1**

```php
// добавляем любой фильтр
$video->addFilter($filter);

// добавляем фильтр с приоритетом = 99.
$audio->addFilter($filter, 99);
```

#### Простой фильтр, SimpleFilter

Это самый простой фильтр, который позволяет устанавливать свои параметры для командной строки ffmpeg.

```php
use Arhitector\Transcoder\Filter\SimpleFilter;
```

**Конструктор**

```php
public SimpleFilter::__construct(array $parameters = [])
```

**Пример №1**

Создадим экземпляр и добавим параметр 'video_codec'.

```php
// 
$filter = new SimpleFilter([
	'video_codec' => 'h264'
]);
```

**Пример №2**

Этот метод перезапишет ранее установленные значения.

```php
$filter->setParameters([
	'video_codec' => 'libx264'
]);

// ArrayAccess
$filter['video_codec'] = 'x264';
```

### Типы фильтров

* Аудио фильтры

Такие фильтры реализуют интерфейс `AudioFilterInterface` и могут использоваться совместно только с `Audio` или `Video`.

* Видео фильтры

Реализуют интерфейсы `FrameFilterInterface` или `VideoFilterInterface`, используются либо с `Frame` либо с `Video`.

### Фильтр Cut

Аудио фильтр, который позволяет обрезать медиа-файл до определённых значений продолжительности.

```php
use Arhitector\Transcoder\Filter\Cut;
```

**Конструктор**

```php
public Cut::__construct(TimeInterval|int $start [, TimeInterval $duration = null])
```

**Пример №1**

Пропустить 20 секунд от начала и сохранить последующие 60 секунд.

```php
$filter = new Cut(new TimeInterval(20), new TimeInterval(60));
```

### Фильтр Volume

Аудио фильтр, который изменяет громкость аудио потока.

```php
use \Arhitector\Transcoder\Filter\Volume;
```

**Конструктор**

```php
public Volume::__construct(float $volume [, string $precision = null])
```

**Пример №1**

Пример показывает как уменьшить громкость аудио.

```php
$filter = new Volume(0.5);
$filter = new Volume(1/2);
$filter = new Volume('6.0206dB');
```

**Пример №2**

Увеличение входной мощности звука на 6 дБ с фиксированной точностью.

```php
$filter = new Volume('6dB', Volume::PRECISION_FIXED);
```

### Фильтр Fade

Фильтр накладывает эффект затухания звука на аудио дорожку.

```php
use \Arhitector\Transcoder\Filter\Fade;
```

**Конструктор**

```php
public Fade::__construct(TimeInterval|int $startTime = 0 [, TimeInterval|int $duration = null [, string $effectType = null]])
```

**Пример №1**

```php
new Fade(2, 10, Fade::FADE_OUT)
```

### Фильтр AudioDelay

```php
use \Arhitector\Transcoder\Filter\AudioDelay;
```

### Фильтр Rotate

```php
use \Arhitector\Transcoder\Filter\Rotate;
```

**Конструктор**

```php
public Rotate::__construct($angle = null)
```

### Фильтр Crop

```php
use \Arhitector\Transcoder\Filter\Crop;
```

**Конструктор**

```php
public Crop::__construct(Point $start, Dimension $dimension)
```

## Лицензия

Распространяется под лицензией <a href="http://opensource.org/licenses/MIT">MIT</a>.

```
Copyright (c) 2017 Dmitry Arhitector

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
