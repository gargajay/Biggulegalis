<?php

return [
    //Configuration Message
    'S3_BUCKET_CREDENTIALS' => 'No se encontraron las credenciales de AWS.',
    'S3_BUCKET_URL' => 'No se encontró la URL de AWS.',
    'MAIL_CREDENTIALS' => 'No se encontraron las credenciales de correo.',
    'SMS_CREDENTIALS' => 'No se encontraron las credenciales de SMS.',
    'STRIPE_CREDENTIALS' => 'Credenciales de Stripe no encontradas.',

    //Route Message
    'PAGE_NOT_FOUND' => 'Página no encontrada.',
    'TOO_MANY_ATTEMPTS' => 'Demasiados intentos. Por favor, inténtalo de nuevo más tarde.',

    //Auth Message
    'UNAUTHORIZED_ACCESS' => 'No autorizado: Por favor, inicie sesión para acceder a este recurso.',
    'LOGIN_FAILED' => 'Credenciales de inicio de sesión no válidas.',
    'LOGIN_SUCCESSFUL' => 'Inicio de sesión exitoso.',
    'SOMETHING_WENT_WRONG' => 'Algo salió mal.',
    'REGISTRATION_SUCCESSFUL' => 'Registro exitoso.',
    'ACCOUNT_BLOCKED' => 'Su cuenta ha sido suspendida.',
    'PROFILE_UPDATED' => 'Su perfil se ha actualizado correctamente.',
    'NOT_FOUND' => 'Información no encontrada.',
    'INVALID_OLD_PASSWORD' => 'La contraseña anterior no coincide.',
    'PASSWORD_RESET_SUCCESS' => 'Su contraseña se ha restablecido correctamente.',
    'PASSWORD_CHANGE_SUCCESS' => 'Su contraseña se ha cambiado correctamente.',
    'NOTIFICATION_SETTINGS' => 'Configuración de notificaciones actualizada con éxito.',
    'LOGOUT_USER' => 'Cierre de sesión exitoso.',
    'PROFILE_FETCHED' => 'Perfil obtenido correctamente.',
    'SETTINGS_FETCHED' => 'La configuración de la aplicación se recuperó correctamente.',
    'MAP_SETTINGS_FETCHED' => 'La configuración del mapa se recuperó correctamente.',
    'MAP_SETTINGS_SAVED' => 'La configuración del mapa se guardó correctamente.',
    'NOTIFICATION_FETCHED' => 'Notificaciones recuperadas con éxito.',
    'NOTIFICATION_READ' => 'Estado de notificación actualizado con éxito.',
    'S3_SECURITY_TOKEN' => 'El token de seguridad para el depósito S3 se generó correctamente.',
    'DELETE_USER' => 'Usuario eliminado correctamente.',

    //API Response
    'RECORD_FETCHED' => 'Registros obtenidos con éxito.',


    //OTP Message
    'OTP_RESEND' => 'Reenvíe los OTP fácilmente después de :seconds segundos.',
    'OTP_SEND_SUCCESS' => 'Se ha enviado una contraseña de un solo uso (OTP).',
    'INVALID_REQUEST' => 'La solicitud no es válida.',
    'OTP_VERIFY_LIMIT' => 'Ha superado el número máximo de intentos para verificar su OTP.',
    'OTP_VERIFY_SUCCESS' => 'La verificación de OTP fue exitosa.',
    'INVALID_OTP' => 'El OTP que ingresó no es válido.',
    'OTP_EXPIRED' => 'Su OTP (contraseña de un solo uso) ha caducado.',
    'PASSWORD_RESET_SESSION_EXPIRE' => 'Su sesión de restablecimiento de contraseña ha caducado.',

    //Forgot email
    'FORGOT_EMAIL_RESEND' => 'Reenvíe el enlace de restablecimiento fácilmente después de :seconds segundos.',
    'FORGOT_EMAIL_SEND_SUCCESS' => 'Se ha enviado el enlace de restablecimiento de contraseña a este correo electrónico.',
    'FORGOT_EMAIL_EXPIRED' => 'Este enlace ha caducado.',


    //Web Admin Message
    'SAVE_SETTINGS' => 'La configuración se ha guardado correctamente.',
    'RECORD_SAVED' => 'El registro se ha guardado correctamente.',
    'RECORD_ACTIVE' => 'El registro se ha activado correctamente.',
    'RECORD_INACTIVE' => 'El registro se ha desactivado correctamente.',
    'RECORD_DELETE' => 'El registro se ha eliminado correctamente.',

    //error page message
    'RESET_LINK_EXPIRED' => [
        'title' => 'Enlace expirado',
        'message' => 'Este enlace de reinicio ha expirado. Por favor, solicite un nuevo enlace.',
    ],


    //API Response message

    // Project
    'CREATE_PROJECT' => 'El proyecto ha sido creado exitosamente.',
    'UPDATE_PROJECT' => 'El proyecto ha sido actualizado exitosamente.',
    'DELETE_PROJECT' => 'El proyecto ha sido eliminado exitosamente.',
    'GET_PROJECT' => 'El proyecto ha sido obtenido exitosamente.',
    'FAVORITE_PROJECT' => 'El proyecto se ha marcado como favorito con éxito.',
    'COMPLETE_PROJECT' => 'El proyecto se ha completado con éxito.',


    // Project Media
    'CREATE_PROJECT_MEDIA' => 'Los medios del proyecto han sido creados exitosamente.',
    'UPDATE_PROJECT_MEDIA' => 'Los medios del proyecto han sido actualizados exitosamente.',
    'DELETE_PROJECT_MEDIA' => 'Los medios del proyecto han sido eliminados exitosamente.',
    'GET_PROJECT_MEDIA' => 'Los medios del proyecto han sido obtenidos exitosamente.',


    // Stripe Card
    'CREATE_STRIPE_CARD' => 'La tarjeta se ha guardado exitosamente.',
    'UPDATE_STRIPE_CARD' => 'La tarjeta se ha actualizado exitosamente.',
    'DELETE_STRIPE_CARD' => 'La tarjeta se ha eliminado exitosamente.',
    'GET_STRIPE_CARD' => 'La tarjeta se ha recuperado exitosamente.',
    'PRIMARY_STRIPE_CARD' => 'La tarjeta ha sido establecida como principal exitosamente.',
    'PRIMARY_OTHER_STRIPE_CARD' => 'Configure otra tarjeta como principal antes de eliminar esta.',


    //Subscription Message
    'SUBSCRIPTION_EXPIRED' => 'Su suscripción ha expirado. Por favor, renueve para continuar usando nuestros servicios.',
    'SUBSCRIPTION_EXPIRED_EMPLOYEE' => 'Su suscripción ha expirado. Por favor, contacte a su administrador para renovar su suscripción.',


    // Employee
    'CREATE_EMPLOYEE' => 'El empleado ha sido guardado exitosamente.',
    'UPDATE_EMPLOYEE' => 'El empleado ha sido actualizado exitosamente.',
    'DELETE_EMPLOYEE' => 'El empleado ha sido eliminado exitosamente.',
    'GET_EMPLOYEE' => 'El empleado ha sido recuperado exitosamente.',
];
