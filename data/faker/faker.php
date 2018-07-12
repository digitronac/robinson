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

echo 'Generated destination!' . PHP_EOL;

/** @var \Robinson\Backend\Models\Images\Destination $destinationImage */
$destinationImage = $di->get(\Robinson\Backend\Models\Images\Destination::class);
$sql = <<<HEREDOC
INSERT INTO destination_images (filename, createdAt, destinationId, sort, extension, width, height)
VALUES ('grcka_destination_1', '2018-07-10 18:16:00', 1, 1, 'jpg', 800, 600)
HEREDOC;
$db->execute($sql);
$fixtureImage = file_get_contents(__DIR__ . '/fixtures/img/destination/1-grcka_destination_1.jpg');
file_put_contents(__DIR__ . '/../../public/img/destination/1-grcka_destination_1.jpg', $fixtureImage);

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

echo 'Generated package!' . PHP_EOL;

$sql = <<<HEREDOC
INSERT INTO package_images (filename, createdAt, packageId, sort, extension, width, height)
VALUES ('vila_maria_1', '2018-07-10 18:16:00', 1, 1, 'jpg', 800, 600)
HEREDOC;
$db->execute($sql);
$fixtureImage = file_get_contents(__DIR__ . '/fixtures/img/package/1-vila_maria_1.jpg');
file_put_contents(__DIR__ . '/../../public/img/package/1-vila_maria_1.jpg', $fixtureImage);

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