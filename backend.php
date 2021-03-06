﻿<?php
//назначаем константы для коннекта к БД
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'guestbook');

$data = $_GET;
$data_post = $_POST;
$messages = array (
	'form_error'       => 'Пожалуйста, заполните все формы корректно',
	'msg_send'         => 'Ваше сообщение успешно отправлено',
	'msg_send_error'   => 'Ошибка отправки сообщения',
	'mysqli_cn_error'  => 'Ошибка соединения с базой данных',
	'mysqli_query_err' => 'Ошибка формирования запроса к базе данных',
	'mysqli_table_cr'  => 'Первый запуск скрипта. Таблица успешно создана'
);
$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die($messages['mysqli_cn_error']);

//условие проверки существования таблицы
$query = "CREATE TABLE IF NOT EXISTS messages(
 	`id` INT(11) NOT NULL AUTO_INCREMENT ,
 	`name` VARCHAR( 25 ) NOT NULL DEFAULT  '',
 	`email` VARCHAR( 50 ) NOT NULL DEFAULT  '',
 	`msg` TEXT,
 	`datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
	PRIMARY KEY ( `id` )
	)";

mysqli_query($dbc, $query);

switch($data['action']){
	case 'get_messages':

		$query = "SELECT id, name, email, msg, UNIX_TIMESTAMP(datetime) as dt FROM messages ORDER BY id DESC LIMIT 5";
		$sql = mysqli_query($dbc, $query) or die($messages['mysqli_query_err']);

		//организуем вывод данных
		while($row   = mysqli_fetch_assoc($sql)) {
			$table_name  = $row['name'];
			$table_email = $row['email'];
			$table_msg   = $row['msg'];
			$table_date  = date('d-m-Y H:i:s',$row['dt']);

			echo "<p><strong>{$table_name}</strong> {$table_email}</p><p>{$table_date}</p>{$table_msg}<hr/>";
		}
break;
}

$data_post['action'] = (empty($data_post['action'])) ? false : $data_post['action'];
switch($data_post['action']){
	case 'add_message':

	$form_name   = trim(strip_tags($data_post['name']));
	$form_email  = trim(strip_tags($data_post['email']));
	$form_msg    = trim(strip_tags($data_post['msg']));

	if(empty($form_name) || empty($form_email) || empty($form_msg)) {
			echo $messages['form_error'];
		} else {
			$query = "INSERT INTO messages (name, email, msg) VALUES ('$form_name', '$form_email', '$form_msg')";
			$sql = mysqli_query($dbc, $query);
		}
		if($sql) {
			echo $messages['msg_send'];
		} else {
			echo $messaqes['msg_send_error'];
		}
break;
}
mysqli_close($dbc);
?>