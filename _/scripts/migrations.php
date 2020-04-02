<?php
/**
 * Файл наката миграций
 *
 * Если get-параметр 'file' содержит номер файла, накатываем поверх, без анализа таблицы migrations
 * Если get-параметр 'save' существует, то метка по этому файлу запишется в базу
 */

$separator = "../../";
include "{$separator}php/ajax/niley4/_ScriptControl.php";
include "{$separator}php/config/site/includes.php";

$dir = "migrations/";
$isTrue = false;
$isSave = false;

echo "<pre style='color:brown'>";
echo "Параметры<br>";
echo "? - без параметров - сканируется migration<br>";
echo "?file=01 - только один файл, без анализа migrations<br>";
echo "?save - метка по этому файлу (по блоку файлов) запишется в базу<br>";
echo "</pre>";


if (isset($_GET['file'])) {
    $regular->search($_GET, ['num']);

    if ($regular->isTrue()) {
        $get = $regular->getResult();
        $files = [$get['file'] . '.sql'];
        $isTrue = true;
    }
    else {
        echo "<pre style='color:red'>";
        echo "Неверное имя файла. Ожидает только цифры";
        echo "</pre>";
    }
}
// обычное сканирование директории с файлами миграций
else {
    $files = scandir($dir);
    $isTrue = true;
}



//сохранить в базу без полной проверки
if (isset($_GET['save'])) {
    $isSave = true;
}


if ($isTrue) {
    $mode = $db->getMode();


    if ($mode == "mysqli") {
        $query = "SHOW TABLES LIKE 'migrations'";
        $result = $db->query($query);

        if ($result->num_rows > 0) {
            // таблица существует
        }
        else {
            echo "<pre style='color:blue'>";
            echo "Таблица migrations не существует";
            echo "</pre>";

            $query = "CREATE TABLE `migrations` (
                `id`	     int(11) NOT NULL auto_increment,
                `file_name`  varchar(20) default '',
                `date`       varchar(20) default '',
                PRIMARY KEY (id))ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            if ($db->query($query)) {
                echo "<pre style='color:green'>";
                echo "Таблица migrations создана";
                echo "</pre>";
            }
        }
    }
    else if ($mode == "sqlite") {
        $query = "CREATE TABLE IF NOT EXISTS `migrations` (
            `id`	     INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            `file_name`  VARCHAR default '',
            `date`       VARCHAR default ''
            )";

        if ($db->query($query)) {
            echo "<pre style='color:green'>";
            echo "Таблица migrations готова к использованию";
            echo "</pre>";
        }
    }


    //проверяем таблицу migrations
    $table = $config['prefix'] . 'migrations';
    $migrations = $db->select($table, 'true', '*');

    if ($files) {
        $count = count($files);
        for ($i = 0; $i < $count; $i++) {
            $file = $files[$i];

            if (
                $file != "."
                && $file != ".."
            ) {
                $a_file = explode(".", $file);

                if ($a_file[1] == "sql") {

                    $isFile = false;

                    //сравнение с таблицей миграций
                    if ($migrations) {
                        foreach ($migrations as $val) {
                            //сравниваем имена файлов
                            if ($val['file_name'] == $a_file[0]) {
                                $isFile = true;
                                break;
                            }
                        }
                    }

                    if (!$isFile) {
                        $fullfile = "{$dir}{$a_file[0]}.sql";
                        fireQuery($fullfile, $a_file[0], $db, $config, $isSave);
                    }
                }
            }
        }
    }
}


/**
 * Выполнить query-запрос для конкретного файла
 */
function fireQuery($file, $fileName, $db, $config, $isSave){
    if (@file_exists($file)) {
        $query = file_get_contents($file);

        //анализ query на наличие пробелов в файлах, и возможность выполнения нескольких запросов
        $a_query = explode(";", $query);

        $total_migration = 0;
        $true_migration = 0;

        $count = count($a_query);
        for ($i=0; $i<$count; $i++) {
            $oneQuery = trim($a_query[$i]);

            if ($oneQuery) {
                $total_migration++;

                if ($db->query($oneQuery)) {
                    echo "<pre style='color:blue'>";
                    echo "Файл {$file}";
                    echo "</pre>";
                    echo "<pre style='color:gray'>";
                    echo $oneQuery;
                    echo "</pre>";
                    echo "<pre style='color:green'>";
                    echo "Выполнено";
                    echo "</pre>";

                    $true_migration++;
                }
                else {

                    echo "<pre style='color:blue'>";
                    echo "Файл {$file}";
                    echo "</pre>";
                    echo "<pre style='color:gray'>";
                    echo $oneQuery;
                    echo "</pre>";
                    echo "<pre style='color:red'>";
                    echo "Ошибка. См. лог: " . $db->getLogObject()->getLogPath();
                    echo "</pre>";
                }
            }
        }

        //запись должна быть только одна (даже если несколько запросов в файле)
        if ($total_migration == $true_migration || $isSave) {

            //Запись данных о миграции в БД
            $table = $config['prefix'] . 'migrations';
            $data = [
                'file_name' => $fileName,
                'date' => date("Y.m.d, H:i:s", time())
            ];

            if ($db->insert($table, $data)) {
                echo "<pre style='color:blue'>";
                echo "+ Запись данных о миграции {$file}";
                echo "</pre>";
                echo "<pre style='color:green'>";
                echo "Выполнено";
                echo "</pre>";
            }
            else {
                echo "<pre style='color:blue'>";
                echo "+ Запись данных о миграции {$file}";
                echo "</pre>";
                echo "<pre style='color:red'>";
                echo "Ошибка. См. лог: " . $db->getLogObject()->getLogPath();
                echo "</pre>";
            }
        }
    }
    else {
        echo "Файл миграций {$file} не найден";
    }
}