﻿<?php
//назначаем константы для коннекта к БД
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'guestbook');

$data = $_GET;
$messages = array (
	'form_error'       => 'Пожалуйста, заполните все формы корректно',
	'msg_send'         => 'Ваше сообщение успешно отправлено',
	'file_write_error' => 'Ошибка записи в файл',
	'file_not_found'   => 'Файл не найден',
	'fread_error'      => 'Файл не может быть прочитан',
	'mysqli_cn_error'  => 'Ошибка соединения с базой данных',
	'mysqli_query_err' => 'Ошибка формирования запроса к базе данных',
	'mysqli_table_cr'  => 'Первый запуск скрипта. Таблица успешно создана'
);
$filename = 'book.txt';
switch($data['action']){
	case 'add_record':

		// задаем основные переменные
		$name     = trim(strip_tags($data['name']));
		$email    = trim(strip_tags($data['email']));
		$txt      = trim(strip_tags($data['msg']));
		$date     = date('Y-m-d G:i:s');
		$redir    = '<meta http-equiv="refresh" content="1; url='.$_SERVER['HTTP_REFERER'].'">';

		echo $redir;

		// проверка на пустоту
		if(empty($name) || empty($email) || empty($txt)) {
			echo $messages['form_error'];

		} else {
			$msg = $date.'|'.$email.'|'.$name.'|'.$txt."\n";

		// открытие и запись файла
			$fp = fopen($filename, 'a+');
			if(fwrite($fp, $msg)){
				echo $messages['msg_send'];
			} else {
				echo $messages['file_write_error'];
			}
		fclose($fp);
		}
	break;

	case 'get_messages':

		// проверка существования файла
		if(file_exists($filename)) {

			//проверка читабельности файла
			if(is_readable($filename)) {

				//открываем файл и помещаем его содержимое в массив
				$content = file($filename);
					if(is_array($content)) {
						$content = array_reverse($content);
						foreach($content as $line) {
							list($date, $email, $name, $msg) = explode('|', $line);
							echo "<p><strong>{$name}</strong> {$email}</p><p>{$date}</p>{$msg}<hr/>";
						}

					}
			}else {
				echo $messages['fread_error'];
			}
		} else {
			echo $messages['file_not_found'];
		}
	break;
}