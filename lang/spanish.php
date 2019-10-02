<?php
# Admin side
// Sidebar menu
$_ADDONLANG['menulog'] = "AutoUnblock csf log";
$_ADDONLANG['menuactions'] = "IP Acciones";
$_ADDONLANG['menumanager'] = "CSF Manager";
$_ADDONLANG['menubackups'] = "Log Backups";
$_ADDONLANG['menuinfo'] = "License Info";
$_ADDONLANG['configservers'] = "Config Servers";
$_ADDONLANG['menuconfig'] = "Configuracion";
$_ADDONLANG['menuhelp'] = "Documentacion";
// AutoUnblock popup
$_ADDONLANG['auactionserver'] = "Servidor";
$_ADDONLANG['auactionip'] = "Su dirección";
// Log Page
$_ADDONLANG['logactivitytitle'] = "AutoUnblock csf :: log de actividad";
$_ADDONLANG['logtabletime'] = "Hora";
$_ADDONLANG['logtableserver'] = "Servidor";
$_ADDONLANG['logtableaction'] = "Acción";
$_ADDONLANG['logtableip'] = "Dirección IP";
$_ADDONLANG['logtabledesc'] = "Log";
$_ADDONLANG['logtableuser'] = "Admin/Client";
$_ADDONLANG['logtablefrom'] = "Petición desde ";
// IP actions
$_ADDONLANG['unblocksuccess'] = "Desbloqueado con éxito:";
$_ADDONLANG['ipacttitle'] = "IP Acciones";
$_ADDONLANG['ipactdes'] = "Por acciones IP, usted puedebloquear, liberar, dejar y pasar por alto la dirección IP.";
$_ADDONLANG['enterip'] = "Introduzca la dirección IP:";
$_ADDONLANG['selectserver'] = "Seleccione Servidor:";
$_ADDONLANG['allservers'] = "Todos los servidores";
$_ADDONLANG['buttonsearch'] = "Buscar";
$_ADDONLANG['buttonsearchdesc'] = "Buscar iptables para la dirección IP";
$_ADDONLANG['buttonunblock'] = "Buscar y liberar";
$_ADDONLANG['buttonunblockdesc'] = "Quite la dirección IP del servidor de seguridad (bloques temporales y permanente)";
$_ADDONLANG['buttonqallow'] = "Permitir rápidamente";
$_ADDONLANG['buttonqallowdesc'] = "Permitir dirección IP a través del firewall y añadir al archivo de permitir (csf.allow)";
$_ADDONLANG['buttonqdeny'] = "Denegar rapidamente";
$_ADDONLANG['buttonqignore'] = "Ignorar rapidamente";
$_ADDONLANG['buttonqignoredesc'] = "No haga caso de la dirección IP en la LFD, agregue al archivo ignorar (csf.ignore) y reinicie LFD";
// CSF Manager
$_ADDONLANG['csftitle'] = "CSF Manager";
$_ADDONLANG['csfdes'] = "Administrar las operaciones en el CSF por sus WHMCS.";
$_ADDONLANG['csfselect'] = "Seleccione Servidor para continuar ...";
$_ADDONLANG['csfexpand'] = "Expandir todo";
$_ADDONLANG['csfcollapse'] = "Contraer todo";
$_ADDONLANG['buttonstatistics'] = "Ver estadísticas";
$_ADDONLANG['buttonstatisticsdesc'] = "Ver LFD estadísticas bloqueo";
$_ADDONLANG['buttonviewlogs'] = "Ver el log de iptables";
$_ADDONLANG['buttonviewlogsdesc'] = "Ver las ultimas 100 lineas del log de iptables";
$_ADDONLANG['buttonrestartq'] = "Firewall :: Reinicio rápido";
$_ADDONLANG['buttonrestartqdesc'] = "Reinicie el csf iptables firewall mediante lfd";
$_ADDONLANG['buttonlfdstatus'] = "Estado lfd";
$_ADDONLANG['buttonlfdstatusdesc'] = "Mostrar el estado lfd";
$_ADDONLANG['buttonlfdrestart'] = "lfd Restart";
$_ADDONLANG['buttonlfdrestartdesc'] = "Restart lfd";
$_ADDONLANG['buttonenable'] = "Activar";
$_ADDONLANG['buttonenabledesc'] = "Permite csf y lfd Si está desactivado previamente";
$_ADDONLANG['buttondisable'] = "Desactivar";
$_ADDONLANG['buttondisabledesc'] = "Desactiva csf y lfd";
$_ADDONLANG['buttonflushall'] = "Vaciar todas las IP";
$_ADDONLANG['buttonflushalldesc'] = "Elimina y desbloquea todas las entradas en csf.deny (excepto los marcados como \"do not delete\") y todas las entradas IP temporales (bloques y permite)";
// Log backup
$_ADDONLANG['logbackupconfirmrestore'] = "Restore sobrescribe el registro de Corrent. Continuar?";
$_ADDONLANG['logbackupconfirmdelete'] = "Eliminar archivos de copia de seguridad?";
$_ADDONLANG['logbackupsuccess'] = "Finalizado";
$_ADDONLANG['logbackuperror'] = "Error";
$_ADDONLANG['logbackuptitle'] = "Log backup manager";
$_ADDONLANG['logbackupdesH'] = "Con el Administrador de copia de seguridad de registro, puede realizar copias de seguridad y restaurar el registro del módulo.";
$_ADDONLANG['logbackupdes'] = "Crear, importar y descargar archivos de copia de seguridad de su registro";
$_ADDONLANG['logbackupbutton'] = "Crear backup";
$_ADDONLANG['logbackuptable1'] = "Backup fecha";
$_ADDONLANG['logbackuptable2'] = "Tamaño";
$_ADDONLANG['logbackuptable3'] = "Acciones";
$_ADDONLANG['logbackupdownload'] = "Download";
$_ADDONLANG['logbackuprestore'] = "Restaurar";
$_ADDONLANG['logbackupdelete'] = "Borrar";
// License Info
$_ADDONLANG['autounblock_licenseinfo'] = "License Info";
$_ADDONLANG['autounblock_licenseinfodes'] = "Detailed information about your software license.";
$_ADDONLANG['autounblock_registeredto'] = "Registered To";
$_ADDONLANG['autounblock_licensekey'] = "License Key";
$_ADDONLANG['autounblock_licensetype'] = "License Type";
$_ADDONLANG['autounblock_validdomain'] = "Valid Domain";
$_ADDONLANG['autounblock_validip'] = "Valid IP";
$_ADDONLANG['autounblock_validdirectory'] = "Valid Directory";
$_ADDONLANG['autounblock_created'] = "Created";
$_ADDONLANG['autounblock_expires'] = "Expires";
# Client side
$_ADDONLANG['menutext'] = "Desbloquear la dirección IP";
$_ADDONLANG['loginmsg1'] = "Su dirección IP se bloquea el acceso a su servidor.";
$_ADDONLANG['loginmsg2'] = "La razón por la que redirige a esta página es para quitar el bloque de IP automáticamente.";
$_ADDONLANG['titletext'] = "Búsqueda bloque firewall de IP en los servidores con cuentas de hosting activos ...";
$_ADDONLANG['presearchtext'] = "Buscar y  eliminaciar de direcciones IP bloqueadas en servidores con cuentas de hosting suyas<br /> Para continuar, por favor, introduzca la dirección IP:";
$_ADDONLANG['tabletitle1'] = "Búsqueda bloque firewall de IP";
$_ADDONLANG['tabletitle2'] = "en servidores con cuentas de hosting activas ...";
$_ADDONLANG['serverstitle'] = "Servidor de su cuenta";
$_ADDONLANG['resultstitle'] = "Resultados de IP bloqueadas";
$_ADDONLANG['theipaddress'] = "La IP ";
$_ADDONLANG['cleanmsg'] = "no aparece como bloqueado el acceso al servidor.";
$_ADDONLANG['blockmsg'] = "fue catalogado como bloqueado el acceso al servidor de hosting. <br /> El bloqueo se elimina y no se requiere ninguna acción de su parte.";
$_ADDONLANG['timendate'] = "Fecha y hora bloqueadas:";
$_ADDONLANG['blockedreason'] = "Log del servidor para este bloqueo de ip:";
$_ADDONLANG['adminblockmsg'] = "fue bloqueado por el administrador del servidor y no se puede eliminar de forma automática. <br /> Por favor, póngase en contacto con apoyo para resolver el problema.";
$_ADDONLANG['limitmsg1'] = "Estimado cliente, desbloqueo no se ejecutará ya que el número de veces que se puede hacer esta acción es limitada. Ponga un ticket de soporte";
$_ADDONLANG['limitmsg2'] = "Usted será capaz de utilizar el desbloqueo automático en ";
$_ADDONLANG['limitdesc'] = "(Horas:minutos:segundos)";
$_ADDONLANG['searchip'] = "Buscar &amp; desbloquear";
// log results
$_ADDONLANG['ldflog_empty'] = "no se puede mostrar la razón del bloqueo";
$_ADDONLANG['ldflog_modsec'] = "El bloqueao ha sido provocada por mod_security. Para obtener más información, póngase en contacto con el soporte.";
$_ADDONLANG['ldflog_ftp'] = "Usted ha llegado al máximo deintentos de conexión a FTP fallidos";
$_ADDONLANG['ldflog_cpanel'] = "Usted ha llegado al máximo deintentos de conexión a Cpanel fallidos";
$_ADDONLANG['ldflog_pop3'] = "Usted ha llegado al máximo deintentos de conexión a POP3 (correo) fallidos";
$_ADDONLANG['ldflog_cxsftp'] = "El archivo que ha subido al servidor es script de potencialmente malicioso";
$_ADDONLANG['ldflog_permbytemp'] = "El bloqueo permanente se ha disparado debido a varios bloqueos temporales";
$_ADDONLANG['ldflog_smtp'] = "Usted ha llegado al máximo deintentos de conexión a SMTP (correo) fallidos";
$_ADDONLANG['ldflog_htpasswd'] = " Error de inicio de sesión (página web|htpasswd)";
$_ADDONLANG['ldflog_ftpdistributed'] = "Ataque distribuido a su cuenta FTP. Para obtener más información, póngase en contacto con el soporte.";
$_ADDONLANG['ldflog_imapdistributed'] = "Ataque distribuido a su cuenta IMAP. Para obtener más información, póngase en contacto con el soporte.";
$_ADDONLANG['ldflog_imap'] = "Usted ha llegado al máximo deintentos de conexión a IMAP (correo) fallidos";
# Version 2.6 Lang variables
$_ADDONLANG['cusmodmenu'] = "Módulos de servidor personalizados";
$_ADDONLANG['cusmodtitle'] = "Manage custom cPanel provisioning modules";
$_ADDONLANG['cusmoddesc'] = "With the help of this tool you can add AutoUnblock csf support to any custom cPanel & DirectAdmin server modules.";
$_ADDONLANG['cusmodadd'] = "Enable";
$_ADDONLANG['cusmoddelete'] = "Disable";
$_ADDONLANG['cusmodthtype'] = "Provisioning Module Type";
$_ADDONLANG['cusmodthstatus'] = "Module Status";
$_ADDONLANG['cusmodnote'] = "* To enable custom cPanel & DirectAdmin server provisioning module, first add it as active server.";
# Version 2.6.2 Lang variables
$_ADDONLANG['ldflog_sshd'] = "Usted ha llegado al máximo deintentos de conexión a SSH (correo) fallidos";
$_ADDONLANG['ldflog_portscan'] = "Escaneo de puertos detectado, usted ha llegado a los número máximos permitido";
# Version 2.6.5 Lang variables
$_ADDONLANG['buttonqdenydesc'] = "Block IP address in the firewall and add to the deny file (csf.deny)";
$_ADDONLANG['excludemenu'] = "Exclude Servers";
$_ADDONLANG['excludetitle'] = "Exclude servers from AutoUnblock csf";
$_ADDONLANG['excludedesc'] = "If you have servers without csf installation you can exclude them from working with AutoUnblock csf here";
$_ADDONLANG['excludethname'] = "Server Name";
$_ADDONLANG['excludethip'] = "IP Address";
$_ADDONLANG['excludethstatus'] = "Server Status";
$_ADDONLANG['excludenote'] = "* Checked servers will work with AutoUnblock csf";
$_ADDONLANG['comment'] = "Comment (Allow & Deny):";
$_ADDONLANG['commenteg'] = "e.g. do not delete";
$_ADDONLANG['commentnote'] = "* Adding the \"do not delete\" comment to a deny will exlude this ip from flush all blocks action";
?>

