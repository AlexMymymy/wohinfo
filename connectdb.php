<?php 
  function GetSettings($param_name)
  {
    global $mysqli;
    $sql="SELECT settings.value
          FROM settings 
          WHERE settings.name=" . $mysqli->real_escape_string($param_name) ." ";
    $result = $mysqli->query($sql);
    if($result->num_rows != 1)
    {
      $result->close();
      return null;
    }
    $r=$result->fetch_row();
    $result->close();
    return $r[0];
  }

  function SetSettings($param_name, $param_value)
  {
    global $mysqli;
    $sql="INSERT INTO settings (name,value)
          VALUES ('"./*$mysqli->real_escape_string(*/$param_name/*)*/."',
          '".$mysqli->real_escape_string($param_value)."')
          ON DUPLICATE KEY UPDATE value=".$mysqli->real_escape_string($param_value)." ";
    $mysqli->query($sql) or die("SetSettings error: " . $mysqli->error);
  }

  //$link = mysql_connect("mysql.100ms.ru", "u482424607_user", "psw123") or die("Could not connect: " . mysql_error());
  //$link = mysql_connect("localhost", "u482424607_user", "psw123") or die("Could not connect: " . mysql_error());
  //mysql_select_db('u482424607_base')                                   or die ('Can\'t use base : ' . mysql_error());

  //$mysqli = new  mysqli("mysql.tinhost.ru", "u148861535_user", "psw123", 'u148861535_base');
  //$mysqli = new  mysqli("mysql.hostinger.ru", "u524606676_user", "psw123", 'u524606676_base');
  $mysqli = new  mysqli("localhost", "a4962533_user", "psw123", 'a4962533_base');
  //if($mysqli->connect_errno)
  //{
  //  $mysqli = new  mysqli("mysql1.000webhost.com", "a4962533_user", "psw123", 'a4962533_base');
    if($mysqli->connect_errno)
    {
      die("Could not connect mysql : " . $mysqli->connect_error);
    }
  //}

  /* изменение набора символов на utf8 */
  $mysqli->set_charset('utf8');
  //mysql_query("SET NAMES cp1251");
  //$mysqli->query("SET NAMES 'utf8';");
  //$mysqli->query("SET CHARACTER SET 'utf8';");
  //$mysqli->query("SET SESSION collation_connection = 'utf8_general_ci';");   
  
  $result = $mysqli->query("SHOW TABLES LIKE 'settings'");
  if($result->num_rows == 0)
  {
    // --------------------------------------------------------
    //
    // Создание таблицы `settings`
    //
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS settings (
      name varchar(16) COLLATE utf8_unicode_ci NOT NULL,
      value varchar(256) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (name)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation settings error: " . $mysqli->error);
    
    // --------------------------------------------------------
    //
    // Создание таблицы `users`
    //
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS users (
      id int(11) NOT NULL AUTO_INCREMENT,
      name varchar(64) COLLATE utf8_unicode_ci NOT NULL,
      password varchar(64) COLLATE utf8_unicode_ci NOT NULL,
      access int(11) NOT NULL,
      PRIMARY KEY (id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation users error: " . $mysqli->error);
    
    // --------------------------------------------------------
    //
    // Создание таблицы `user_cookie`
    //
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS user_cookie (
      date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      user_id int(11) NOT NULL,
      cook_val varchar(128) COLLATE utf8_unicode_ci NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation user_cookie error: " . $mysqli->error);
    
    //--------------------------------------------------------
    //
    // Создание таблицы `postdata`
    //
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS postdata (
      date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      extip varchar(16) COLLATE utf8_unicode_ci NOT NULL,
      name varchar(32) COLLATE utf8_unicode_ci NOT NULL,
      value varchar(65536) COLLATE utf8_unicode_ci NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation postdata error: " . $mysqli->error);

    //--------------------------------------------------------
    //
    // Создание таблицы `city`
    //
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS city (
      id int(11) NOT NULL,
      name varchar(64) COLLATE utf8_unicode_ci NOT NULL,
      player_id int(6) NOT NULL,
      chudo_id smallint(3),
      chudo_lvl tinyint(2),
      climate_id tinyint(2) DEFAULT 0,
      hill tinyint(1),
      population int(8),
      pos_ref smallint(5) DEFAULT 0,
      pos_x smallint(5),
      pos_y smallint(5),
      PRIMARY KEY (id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation city error: " . $mysqli->error);

    // --------------------------------------------------------
    //
    // Создание таблицы `country`
    //
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS country (
      id int(11) NOT NULL,
      name varchar(64) COLLATE utf8_unicode_ci NOT NULL,
      population int(8),
      PRIMARY KEY (id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation country error: " . $mysqli->error);

    // --------------------------------------------------------
    //
    // Создание таблицы `player`
    //
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS player (
      id int(11) NOT NULL,
      name varchar(64) COLLATE utf8_unicode_ci NOT NULL,
      country_id int(11) NOT NULL,
      race_id tinyint(2),
      sex_id tinyint(2),
      role_id int(11),
      population int(8),
      has_oracul BOOL,
      PRIMARY KEY (id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation player error: " . $mysqli->error);

    // 
    // Создание таблицы `population`
    // 
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS population (
      city_id int(11) NOT NULL,
      date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      population int(11) NOT NULL,
      KEY city_id (city_id,date)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation population error: " . $mysqli->error);
    
    // 
    // Создание таблицы `deposit`
    // 
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS deposit (
      city_id int(11) NOT NULL,
      date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      deposit_id smallint(11),
      KEY city_id (city_id,date)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation deposit error: " . $mysqli->error);
    
    //--------------------------------------------------------
    //
    // Создание таблицы `city_build`
    //
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS city_build (
      city_id int(11) NOT NULL,
      slot smallint(2),
      build_id smallint(2),
      level smallint(2)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation city_build error: " . $mysqli->error);

    // 
    // Создание таблицы `deposit_name`
    // 
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS deposit_name (
      id smallint(11) NOT NULL,
      name varchar(64) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation deposit_name error: " . $mysqli->error);

    // 
    // Создание таблицы `chudo_name`
    // 
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS chudo_name (
      id smallint(5) NOT NULL,
      name varchar(64) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation chudo_name error: " . $mysqli->error);

    // 
    // Создание таблицы `unit`
    // 
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS unit (
      id int(11) NOT NULL,
      name varchar(64) COLLATE utf8_unicode_ci NOT NULL,
      pict_offset smallint(5),
      PRIMARY KEY (id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation unit error: " . $mysqli->error);
    
    // 
    // Создание таблицы `climate_name`
    // 
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS climate_name (
      id tinyint(2) NOT NULL,
      name varchar(64) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation climate_name error: " . $mysqli->error);

    //
    // Создание таблицы `build_name`
    //
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS build_name (
      build_id smallint(2),
      name varchar(64) COLLATE utf8_unicode_ci NOT NULL,
      pict_offset smallint(5),
      PRIMARY KEY (build_id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation build_name error: " . $mysqli->error);

    // 
    // Создание таблицы `atak_in`
    // 
    $mysqli->query("
    CREATE TABLE IF NOT EXISTS atak_in (
      id int(10) NOT NULL,
      from_id int(6) NOT NULL,
      to_id int(6) NOT NULL,
      from_time TIMESTAMP,
      to_time TIMESTAMP,
      speed smallint(2),
      type smallint(2),
      PRIMARY KEY (id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ") or die("Creation atak_in error: " . $mysqli->error);



    // 
    // Заполнение таблицы `users` константами
    // 
    $mysqli->query("
    INSERT INTO users (name, password, access) 
      VALUES ('Admin','1', 127)
            ,('Alex', '1', 1);
    ") or die ("Filling users error: " . $mysqli->error);


    // 
    // Заполнение таблицы `unit` константами
    // 
    //$mysqli->query("


    // 
    // Заполнение таблицы `build_name` константами
    // 
    $mysqli->query("
    INSERT INTO build_name (build_id, name, pict_offset) 
      VALUES (0,'Алтарь',0)
            ,(1,'Бойцовская яма', 0)
            ,(2,'Землянка', 0)
            ,(3,'Ров', 0)
            ,(4,'Колодец', 0)
            ,(5,'Лесоповал', 0)
            ,(6,'Ферма', 0)
            ,(7,'Казарма', 0)
            ,(8,'Обелиск', 0)
            ,(10,'Конюшня', 0)
            ,(12,'Гранитный карьер', 0)
            ,(13,'Посольство', 0)
            ,(14,'Суд', 0)
            ,(15,'Стрельбище', 0)
            ,(16,'Верфь', 0)
            ,(17,'Склад', 0)
            ,(18,'Типи', 0)
            ,(19,'Частокол', 0)
            ,(20,'Пристань', 0)
            ,(21,'Шахта', 0)
            ,(22,'Винодельня', 0)
            ,(23,'Скотный двор', 0)
            ,(24,'Промысловый порт', 0)
            ,(25,'Резиденция', 0)
            ,(26,'Рынок', 0)
            ,(27,'Дом охотника', 0)
            ,(28,'Водозабор', 0)
            ,(32,'Мастерская', 0)
            ,(33,'Философская школа', 0)
            ,(34,'Фонтан', 0)
            ,(35,'Библиотека', 0)
            ,(36,'Дом ткача', 0)
            ,(37,'Больница', 0)
            ,(38,'Частокол', 0)
            ,(39,'Замок', 0)
            ,(40,'Банк', 0)
            ,(41,'Военный завод', 0)
            ,(42,'Типография', 0)
            ,(44,'Порт', 0)
            ,(45,'Хранилище такаюка', 0)
            ,(46,'Топливный завод', 0)
            ,(47,'Киностудия', 0)
            ,(48,'Дом', 0)
            ,(49,'Мануфактура', 0)
            ,(51,'Монетный двор', 0)
            ,(52,'Ткацкая фабрика', 0)
            ,(53,'Ратуша', 0)
            ,(54,'Завод', 0)
            ,(55,'Мэрия', 0)
            ,(56,'Геоглиф', 0)
            ,(57,'Укрепрайон', 0)
            ,(59,'Космический завод', 0)
            ,(61,'Университет', 0)
            ,(62,'Музей', 0)
            ,(63,'Собор', 0)
            ,(66,'Обогатительный завод', 0)
            ,(67,'Театр', 0)
            ,(68,'Статуя', 0)
            ,(69,'Маяк', 0)
            ,(70,'Академия', 0)
            ,(71,'Водопровод', 0)
            ,(72,'Лаборатория', 0)
            ,(74,'Военная часть', 0)
            ,(79,'Храм', 0)
            ,(80,'Обсерватория', 0)
            ,(81,'Госпиталь', 0)
            ,(82,'Монастырь', 0)
            ,(84,'Ловушка для рыбы', 0)
            ,(85,'Хижина', 0)
            ,(86,'Тайник', 0)
            ,(87,'Площадь', 0)
            ,(88,'Петроглиф', 0)
            ,(89,'Сероплавильный завод', 0)
            ,(90,'Кузница', 0)
            ,(93,'Ангар', 0)
            ,(96,'Дом собирателя', 0)
            ,(101,'Зенитная башня', 0)
            ,(103,'Радиолокационная станция', 0)
            ,(104,'Торговая база', 0)
            ,(105,'Муляж', 0)
            ,(106,'Очистная станция', 0)
            ,(109,'Система ПВО', 0)
            ,(110,'Станция маниту', 0)
            ,(111,'Мохоро вождей', 0)
            ,(500,'---', 0);
    ") or die ("Filling climate_name error: " . $mysqli->error);

    // 
    // Заполнение таблицы `climate_name` константами
    // 
    $mysqli->query("
    INSERT INTO climate_name (id, name) 
      VALUES (0,'---')
            ,(1,'луг')
            ,(2,'степь')
            ,(3,'пустыня')
            ,(4,'снега');
    ") or die ("Filling climate_name error: " . $mysqli->error);

    // 
    // Заполнение таблицы `deposit_name` константами
    // 
    $mysqli->query("
    INSERT INTO deposit_name (id, name) 
      VALUES ( 0,'')
            ,( 1,'Лес')
            ,( 2,'Оазис')
            ,( 3,'Бананы')
            ,( 4,'Яблоки')
            ,( 5,'Абрикосы')
            ,( 6,'Виноград')
            ,( 7,'Кукуруза')
            ,( 8,'Пшеница')
            ,( 9,'Рис')
            ,(10,'Рыба')
            ,(11,'Киты')
            ,(12,'Крабы')
            ,(13,'Устрицы')
            ,(14,'Свиньи')
            ,(15,'Коровы')
            ,(16,'Олени')
            ,(17,'Овцы')
            ,(18,'Хлопок')
            ,(19,'Лен')
            ,(20,'Золото')
            ,(21,'Серебро')
            ,(22,'Алмазы')
            ,(23,'Изумруды')
            ,(24,'Рубины')
            ,(25,'Жемчуг')
            ,(26,'Железная руда')
            ,(27,'Гранит')
            ,(28,'Лошади')
            ,(29,'Верблюды')
            ,(30,'Слоны')
            ,(31,'Серная руда')
            ,(32,'Природный газ')
            ,(33,'Нефть')
            ,(34,'Уголь')
            ,(35,'Уран')
            ,(36,'Источник мудрости')
            ;
    ") or die ("Filling deposit_name error: " . $mysqli->error);

    
    SetSettings('Version', '1');

  }
  $result->close();
?>