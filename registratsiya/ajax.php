<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");


if($_SERVER["REQUEST_METHOD"] == 'POST' && strlen($_POST['USER_EMAIL']) > 3) {

    $response = [];

    /*
     * В любом случае получаем пользователя по эмейлу
     */
    $user_email = $_POST['USER_EMAIL'];
    $user = new CUser;
    $arParams["SELECT"] = array("UF_REGISTRATION_KEY");
    $filter = array("EMAIL" => $user_email);
    $cUser = $user::GetList(($by = "id"), ($order = "desc"), $filter, $arParams)->fetch();



    /*
     * Если это не этап сохранения пользователя со всеми данными
     */
    if($_POST['TYPE'] != 'SAVE_USER') {

        if ($_POST['TYPE'] == 'REG-STEP_ONE') {

            if ($cUser) {

                /*
                 *  Код на ответ существующего пользователя с таким эмейлом
                 *  выдаем форму на отправку повторного письма
                 */
                if($cUser['ACTIVE'] != 'Y') {
                    $response['error'] = 5;
                    $response['message'] = 'Пользователь с таким эмейлом существует';
                } else {
                    $response['error'] = 6;
                    $response['message'] = 'Пользователь с таким эмейлом существует и активирован';
                }


            } else { // Если пользователя не существует создаем анонимные данные

                $login = 'anonimous_' . $user_email;
                $password = md5(rand(1000, 10000) . __FILE__);
                $registration_key = md5(md5($user_email . __FILE__));
                $url_reg = 'https://' . $_SERVER['HTTP_HOST'] . '/registratsiya/steptwo.php?qwe=' . $registration_key;


                $arFields = array(
                    "EMAIL" => $user_email,
                    "LOGIN" => $login,
                    "LID" => SITE_ID,
                    "ACTIVE" => "N",
                    "PASSWORD" => $password,
                    "CONFIRM_PASSWORD" => $password,
                    "UF_REGISTRATION_KEY" => $registration_key
                );


                $ID = $user->Add($arFields);
                if (intval($ID) > 0) {

                    $fields = array(
                        "USER_EMAIL" => $user_email,
                        "URL_REG" => $url_reg,
                    );

                    CEvent::Send('NEW_REGISTRATION', SITE_ID, $fields);


                    $response['succes'] = 1;
                    $response['message'] = 'Для продолжения регистрации вам было отправлено письмо с ссылкой!';

                } else {
                    $response['error'] = 0;
                    $response['message'] = 'Не удалось создать пользователя, попробуйте заново!';
                }

            }

        }

        /*
         * Если запросили повторную отправку письма
         */

        if ($_POST['TYPE'] == 'REPEAT-SEND') {

            $url_reg = 'https://' . $_SERVER['HTTP_HOST'] . '/registratsiya/steptwo.php?qwe=' . $cUser['UF_REGISTRATION_KEY'];

            $fields = array(
                "USER_EMAIL" => $user_email,
                "URL_REG" => $url_reg,
            );

            CEvent::Send('NEW_REGISTRATION', SITE_ID, $fields);

            $response['succes'] = 1;
            $response['message'] = 'Письмо было повторно отправленно на указанный эмейл';

        }
    }


    /*
     * Если этап сохранения данных, сверяем что не было подмены и совпадают ключи и эмейлы
     */

    if($cUser && $_POST['TYPE'] == 'SAVE_USER' && $cUser['UF_REGISTRATION_KEY'] == $_POST['USER_QWE'] && $_POST['USER_EMAIL'] == $cUser['EMAIL']) {

        if(strlen($_POST['USER_NAME']) < 2) {
            $response['error'] = 1;
            $response['messages'][] = 'Поле Имя обязательно для заполнения';
        }

        if(strlen($_POST['USER_LASTNAME']) < 2) {
            $response['error'] = 1;
            $response['messages'][] = 'Поле Фамилия обязательно для заполнения';
        }

        if(strlen($_POST['USER_LOGIN']) < 2) {
            $response['error'] = 1;
            $response['messages'][] = 'Поле Логин обязательно для заполнения';
        }

        if(strlen($_POST['USER_PHONE']) < 2) {
            $response['error'] = 1;
            $response['messages'][] = 'Поле Телефон обязательно для заполнения';
        }

        if(strlen($_POST['USER_PASSWORD']) < 2) {
            $response['error'] = 1;
            $response['messages'][] = 'Поле Пароль обязательно для заполнения';
        }

        if(strlen($_POST['USER_RPASSWORD']) < 2) {
            $response['error'] = 1;
            $response['messages'][] = 'Поле Подтверждение пароля обязательно для заполнения';
        }

        if($_POST['USER_PASSWORD'] != $_POST['USER_RPASSWORD']) {
            $response['error'] = 1;
            $response['messages'][] = 'Пароли не совпадают';
        }

        if($response['error'] == 1) {
            echo json_encode($response);
            exit();
        }



        $user = new CUser;
        $fields = Array(
            "NAME"              => $_POST['USER_NAME'],
            "LAST_NAME"         => $_POST['USER_LASTNAME'],
            "LOGIN"             => $_POST['USER_LOGIN'],
            "PHONE"             => $_POST['USER_PHONE'],
            "GROUP_ID"          => array(3, 4, 5),
            "ACTIVE"            => "Y",
            "PASSWORD"          => $_POST['USER_PASSWORD'],
            "CONFIRM_PASSWORD"  => $_POST['USER_RPASSWORD'],
        );
        $user->Update($cUser['ID'], $fields);

        $response['succes'] = 5;
        $response['message'] = 'Регистрация пройдена, редиректим на другую страницу';

    }

    echo json_encode($response);

}