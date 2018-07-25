<?php
// autoload
include __DIR__ . '/../../vendor/autoload.php';

// zf style env
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ?: 'development'));
use Phalcon\Mvc\Application;

error_reporting(E_ALL);

define('APPLICATION_PATH', realpath(__DIR__ . '/../../apps'));

/**
 * Include services
 */
require __DIR__ . '/../../config/services.php';

ini_set('display_errors', 1);

/**
 * Handle the request
 */
$application = new Application();

/**
 * Assign the DI
 */
$application->setDI($di);
/**
 * Include modules
 */
require __DIR__ . '/../../config/modules.php';

include __DIR__ . '/../../apps/backend/Module.php';
$backendModule = new \Robinson\Backend\Module();
$backendModule->registerAutoloaders();
$backendModule->registerServices($di);

$translator = new \Zend\Mvc\I18n\Translator();
$translator->addTranslationFile(
    'phparray',
    APPLICATION_PATH . '/../data/translations/zend_validate/sr.php',
    'default',
    'sr'
);
$translator->setLocale('sr');
\Zend\Validator\AbstractValidator::setDefaultTranslator($translator);

$faker = \Faker\Factory::create();
/** @var \Robinson\Backend\Models\Category $category */
$category = $di->get(\Robinson\Backend\Models\Category::class);
$category->setCategory('Grčka');
$category->setStatus(1);
$category->setDescription($faker->text());
$category->setImages([]);
$category->create();

/** @var \Robinson\Backend\Models\Category $grckaCategory */
$grckaCategory = \Robinson\Backend\Models\Category::findFirst(1);

/** @var \Robinson\Backend\Models\Category $category */
$category = $di->get(\Robinson\Backend\Models\Category::class);
$category->setCategory('Španija');
$category->setStatus(1);
$category->setDescription($faker->text());
$category->setImages([]);
$category->create();
/** @var \Robinson\Backend\Models\Category $spanijaCategory */
$spanijaCategory = \Robinson\Backend\Models\Category::findFirst(2);

/** @var \Robinson\Backend\Models\Category $category */
$category = $di->get(\Robinson\Backend\Models\Category::class);
$category->setCategory('Italija');
$category->setStatus(1);
$category->setDescription($faker->text());
$category->setImages([]);
$category->create();
/** @var \Robinson\Backend\Models\Category $italijaCategory */
$italijaCategory = \Robinson\Backend\Models\Category::findFirst(3);

/** @var \Robinson\Backend\Models\Category $category */
$category = $di->get(\Robinson\Backend\Models\Category::class);
$category->setCategory('City Break');
$category->setStatus(1);
$category->setDescription($faker->text());
$category->setImages([]);
$category->create();
/** @var \Robinson\Backend\Models\Category $cityBreakCategory */
$cityBreakCategory = \Robinson\Backend\Models\Category::findFirst(4);

/** @var \Robinson\Backend\Models\Category $category */
$category = $di->get(\Robinson\Backend\Models\Category::class);
$category->setCategory('InSide Serbia');
$category->setStatus(1);
$category->setDescription($faker->text());
$category->setImages([]);
$category->create();

$sql = <<<HEREDOC
UPDATE categories SET categoryId = 14 WHERE categoryId = 5
HEREDOC;
$di->get('db')->execute($sql);


/** @var \Robinson\Backend\Models\Category $insideSerbiaCategory */
$insideSerbiaCategory = \Robinson\Backend\Models\Category::findFirst(14);

echo 'Generated tabs categories!' . PHP_EOL;

/** @var \Phalcon\Db\Adapter\Pdo\Mysql $db */
$db = $di->getShared('db');


/** @var \Robinson\Backend\Models\Destination $grckaDestination */
$grckaDestination = $di->get(\Robinson\Backend\Models\Destination::class);
$grckaDestination->setDestination('Kefalonija');
$grckaDestination->setDescription($faker->text());
$grckaDestination->setStatus(1);
$grckaDestination->setCategoryId($grckaCategory->getCategoryId());
$grckaDestination->create();

/** @var \Robinson\Backend\Models\Destination $insideSerbiaDestination */
$insideSerbiaDestination = $di->get(\Robinson\Backend\Models\Destination::class);
$insideSerbiaDestination->setDestination('The Colors Of Balkan');
$description = <<<HEREDOC
Serbia and the Balkan countries are with same culture and tradition. You are crossing borders but you have always the same theme for traveling. Is it a Monastery Tour, Hiking Tour, Wine tasting Tour, Archeological Tour, Danube or Adriatic Cruise or a Cultural Tour you can always combine several countries in same area. Serbia, Romania, Bulgaria, Greece, Macedonia, Albania, Montenegro, Bosnia and Croatia are good connected with planes, roads and railway. Visit Balkan with Robinson Adventure Team.



In a place where the Orient meets Europe, where the Pagans mix with Christians, Antic develop in modern, has the wild Balkan found his home.   The land rises with honey and blood, with gods and heroes, with legends and kings, with witches and vampires. Protected with the Danube and high mountains, with the sea and the sky, Balkan has an ideal location. You will find nowhere such different history, cultural colors and folklore tradition. Visiting Balkan is like travelling with a Time Machine. You will enjoy the local food and wine, domestic hospitality, impressive nature, wild rivers and lakes, mountains and national parks rich in flora and fauna. The cold stones of the monasteries and castles will tell you stories about wars, culture and love. Balkan is the most romantic place you can visit. From Belgradeto Sremski Karlovci, from Sofia to Sibiu, from Athens to Corfu, from Ohrid to Berat, from Saranda to Mostar and Sarajevo you will see different culture but you will feel Balkan like one country. Discover Balkan on a unique way and adventure the Danube, hike the Drina valley, foul in love in Plovdiv, do your philosophy in Delphi, drink wine in Macedonia, cruise in the bay of Kotor, walk the pedestrian streets of Dubrovnik, adventure rafting in the Tara canyon and feel the legends walking over the bridge of  Visegrad. Do the Balkan Tourwith Robinson Adventure Team.
HEREDOC;

$insideSerbiaDestination->setDescription($description);
$insideSerbiaDestination->setStatus(1);
$insideSerbiaDestination->setCategoryId($insideSerbiaCategory->getCategoryId());
$insideSerbiaDestination->create();
$destinationId = $insideSerbiaDestination->getDestinationId();
$sql = <<<HEREDOC
UPDATE destinations SET destinationId = 48 WHERE destinationId = $destinationId
HEREDOC;
$di->get('db')->execute($sql);
$insideSerbiaDestination = \Robinson\Backend\Models\Destination::findFirst(48);

/** @var \Robinson\Backend\Models\Destination $insideSerbia2Destination */
$insideSerbia2Destination = $di->get(\Robinson\Backend\Models\Destination::class);
$insideSerbia2Destination->setDestination('Discover Cities');
$description = <<<HEREDOC
he cities in Serbia and the Balkans are thru treasures of living from all periods of the civilization. The modern and traditional cities developed from the past find their places on the crossroads, river banks and on the Mediterranean coast. 

Vibrant capitals like Belgrade, Bucharest, Sofia and Athens, romantic cities like Plovdiv, Sremski Karlovci, Mostar, Kotor and Tirana and cultural cities like Dubrovnik, Novi Sad, Ohrid, Sarajevo and Split invites you for walking the pedestrian streets, visit Museums and Galleries, enjoy the Opera and variety of different Concerts. The Gastronomy and wine is really unique and for all sense. Belgrade is famous with his night life and romantic charm. The restaurants combine tradition and local organic products. All of them have original musicians playing romantic songs and nice melodies. Bucharest and Sofia have now more modern restaurants and the new lifestyle. Tirana, Skopje and Sarajevo still have some oriental soul and good hospitality. The Greek and Roman tradition you can feel on the pedestrian streets in the Mediterranean cities like Athens, Corfu, Dubrovnik or Split. Discovering the Balkan cities you will have the opportunity to enjoy the colors of the Balkan heritage.
HEREDOC;

$insideSerbia2Destination->setDescription($description);
$insideSerbia2Destination->setStatus(1);
$insideSerbia2Destination->setCategoryId($insideSerbiaCategory->getCategoryId());
$insideSerbia2Destination->create();
$destination2Id = $insideSerbia2Destination->getDestinationId();
$sql = <<<HEREDOC
UPDATE destinations SET destinationId = 49 WHERE destinationId = $destination2Id
HEREDOC;
$di->get('db')->execute($sql);
$insideSerbia2Destination = \Robinson\Backend\Models\Destination::findFirst(49);

echo 'Generated destinations!' . PHP_EOL;

/** @var \Robinson\Backend\Models\Images\Destination $destinationImage */
$destinationImage = $di->get(\Robinson\Backend\Models\Images\Destination::class);
$sql = <<<HEREDOC
INSERT INTO destination_images (filename, createdAt, destinationId, sort, extension, width, height)
VALUES ('grcka_destination_1', '2018-07-10 18:16:00', 1, 1, 'jpg', 800, 600)
HEREDOC;
$db->execute($sql);
$fixtureImage = file_get_contents(__DIR__ . '/fixtures/img/destination/1-grcka_destination_1.jpg');
file_put_contents(__DIR__ . '/../../public/img/destination/1-grcka_destination_1.jpg', $fixtureImage);

/** @var \Robinson\Backend\Models\Images\Destination $destinationImage */
$destinationImage = $di->get(\Robinson\Backend\Models\Images\Destination::class);
$sql = <<<HEREDOC
INSERT INTO destination_images (filename, createdAt, destinationId, sort, extension, width, height)
VALUES ('thecolorsofbalkan_destination_1', '2018-07-10 18:16:00', 48, 1, 'jpg', 800, 600)
HEREDOC;
$db->execute($sql);
$fixtureImage = file_get_contents(__DIR__ . '/fixtures/img/destination/1-grcka_destination_1.jpg');
file_put_contents(__DIR__ . '/../../public/img/destination/2-thecolorsofbalkan_destination_1.jpg', $fixtureImage);

/** @var \Robinson\Backend\Models\Images\Destination $destinationImage */
$destinationImage = $di->get(\Robinson\Backend\Models\Images\Destination::class);
$sql = <<<HEREDOC
INSERT INTO destination_images (filename, createdAt, destinationId, sort, extension, width, height)
VALUES ('discovercities_destination_1', '2018-07-10 18:16:00', 49, 1, 'jpg', 800, 600)
HEREDOC;
$db->execute($sql);
$fixtureImage = file_get_contents(__DIR__ . '/fixtures/img/destination/1-grcka_destination_1.jpg');
file_put_contents(__DIR__ . '/../../public/img/destination/3-discovercities_destination_1.jpg', $fixtureImage);


echo 'Added image to destination!' . PHP_EOL;

$desc = $faker->text();
$sql = <<<HEREDOC
INSERT INTO packages (package, description, price, pdf, pdf2, status, slug, createdAt, updatedAt, destinationId, `type`, special)
VALUES ('Vila Maria /Ex Nikos/', '{$desc}', 299, '321.pdf', '', 1, 'grcka-leto-2018/kefalonija/vila-maria-ex-nikos', '2018-07-09 12:00:00', '2018-07-09 12:00:00', 1, 1, 0)
HEREDOC;
$db->execute($sql);
if (!file_exists(__DIR__ . '/../../public/pdf/package/321')) {
    mkdir(__DIR__ . '/../../public/pdf/package/321', 0777, true);
}
file_put_contents(
    __DIR__ . '/../../public/pdf/package/321/321.pdf',
    file_get_contents(__DIR__ . '/fixtures/pdf/package/321/321.pdf')
);

$desc = $faker->text();
$sql = <<<HEREDOC
INSERT INTO packages (packageId, package, description, price, pdf, pdf2, status, slug, createdAt, updatedAt, destinationId, `type`, special)
VALUES (466, '13 days Serbia, Montenegro, Croatia', '{$desc}', 45, '321.pdf', '', 1, 'inside-serbia/the-colors-of-balkan/13-days-serbia-montenegro-croatia', '2018-07-09 12:00:00', '2018-07-09 12:00:00', 48, 1, 0)
HEREDOC;
$db->execute($sql);
if (!file_exists(__DIR__ . '/../../public/pdf/package/321')) {
    mkdir(__DIR__ . '/../../public/pdf/package/321', 0777, true);
}
file_put_contents(
    __DIR__ . '/../../public/pdf/package/321/321.pdf',
    file_get_contents(__DIR__ . '/fixtures/pdf/package/321/321.pdf')
);

$desc = $faker->text();
$sql = <<<HEREDOC
INSERT INTO packages (packageId, package, description, price, pdf, pdf2, status, slug, createdAt, updatedAt, destinationId, `type`, special)
VALUES (336, 'Belgrade City Break', '{$desc}', 79, '321.pdf', '', 1, 'inside-serbia/discover-cities/belgrade-city-break', '2018-07-09 12:00:00', '2018-07-09 12:00:00', 49, 1, 0)
HEREDOC;
$db->execute($sql);
if (!file_exists(__DIR__ . '/../../public/pdf/package/321')) {
    mkdir(__DIR__ . '/../../public/pdf/package/321', 0777, true);
}
file_put_contents(
    __DIR__ . '/../../public/pdf/package/321/321.pdf',
    file_get_contents(__DIR__ . '/fixtures/pdf/package/321/321.pdf')
);

echo 'Generated package!' . PHP_EOL;

$sql = <<<HEREDOC
INSERT INTO package_images (filename, createdAt, packageId, sort, extension, width, height)
VALUES ('vila_maria_1', '2018-07-10 18:16:00', 1, 1, 'jpg', 800, 600)
HEREDOC;
$db->execute($sql);
$fixtureImage = file_get_contents(__DIR__ . '/fixtures/img/package/1-vila_maria_1.jpg');
file_put_contents(__DIR__ . '/../../public/img/package/1-vila_maria_1.jpg', $fixtureImage);

$sql = <<<HEREDOC
INSERT INTO package_images (filename, createdAt, packageId, sort, extension, width, height)
VALUES ('13days', '2018-07-10 18:16:00', 466, 1, 'jpg', 800, 600)
HEREDOC;
$db->execute($sql);
$fixtureImage = file_get_contents(__DIR__ . '/fixtures/img/package/1-vila_maria_1.jpg');
file_put_contents(__DIR__ . '/../../public/img/package/2-13days.jpg', $fixtureImage);

$sql = <<<HEREDOC
INSERT INTO package_images (filename, createdAt, packageId, sort, extension, width, height)
VALUES ('belgrade_city_break', '2018-07-10 18:16:00', 336, 1, 'jpg', 800, 600)
HEREDOC;
$db->execute($sql);
$fixtureImage = file_get_contents(__DIR__ . '/fixtures/img/package/1-vila_maria_1.jpg');
file_put_contents(__DIR__ . '/../../public/img/package/3-belgrade_city_break.jpg', $fixtureImage);

echo 'Added image to package!' . PHP_EOL;

$packageTabs = [
    'Program puta' => ['text' => $faker->text(), 'type' => 1],
    'Uslovi putovanja' => ['text' => $faker->text(), 'type' => 2],
    'Avio prevoz' => ['text' => $faker->text(), 'type' => 3],
    'Bus prevoz' => ['text' => $faker->text(), 'type' => 4],
    'Važne napomene' => ['text' => $faker->text(), 'type' => 5],
];
foreach ($packageTabs as $title => $info) {
    $sql = <<<HEREDOC
INSERT INTO package_tabs (title, description, `type`, packageId, createdAt, updatedAt) 
VALUES ("%s", "%s", %d, 1, "2018-07-12 14:32:00", "2018-07-12 14:32:00")
HEREDOC;
    $query = sprintf($sql, $title, $info['text'], $info['type']);
    $db->execute($query);
}
echo 'Generated package tabs!' . PHP_EOL;

/** @var \Robinson\Backend\Models\Tags\Package $packageTag */
$packageTag = $di->get(\Robinson\Backend\Models\Tags\Package::class);
$packageTag->setPackageId(466);
$packageTag->setType(6);
$packageTag->setTag('Inside_Last Minute');
$packageTag->setOrder(1);
$packageTag->create();

echo 'Generated package tags!' . PHP_EOL;