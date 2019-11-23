<?php
/**
 *  ePartool Downloader
 *  https://github.com/DeutscherBundesjugendring/epartool-downloader
 * 
 *  Copyright 2018 Tim Schrock <tim.schrock@dbjr.de>
 * 
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 * 
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 * 
 *  A copy of the GNU Affero General Public License can be retrieved
 *  at <https://www.gnu.org/licenses/agpl-3.0.html>.
 * 
 * */
 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 1);
error_reporting(E_ALL);

define("EPT_DL_VERSION", "1.4 (2018-11-23)"); // ePartool downloader script version

if ($_SERVER['QUERY_STRING'] == "download")  {

    /* This adds header for auto refreshing after download */

    header("refresh:4;url=" . $_SERVER["REQUEST_URI"] . "-run");
    
}

/* Check if https is set or http was deliberately chosen */

if (isset($_GET["encryption"]) && ($_GET["encryption"]=="off")) { 
    
    $security = "okay";

} else {

    $security = "insecure";
    
}

$stopper = "no"; // Will be set to yes if PHP is too old. Downloader will quit. TODO also if memory_limit too low and can't be set higher


/* Try to set memory_limit to 256M if lower */

$memory = ini_get('memory_limit');

if ($memory < "256M") { 
    
    if ( ini_set('memory_limit', "256M") === true ) {

        ini_set('memory_limit', "256M"); 
        
        }
}

/* Try to set max_execution_time to 60 seconds if lower */

$allowedduration =    ini_get('max_execution_time');

if ($allowedduration < 60) {
    
        if ( ini_set('max_execution_time', 60) === true ) {

        ini_set('max_execution_time', 60); 
        
        }
}
    

/* User language detection - so far only German and everyone else is set to English */

if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == "de") {
    
        $txt = array(
        "autodown" => "Automatischer Downloader",
        "btn_begin_download" => "Download starten",
        "downloading" => "Download läuft",
        "plswait" => "Bitte warten, bis das ePartool heruntergeladen und entpackt ist.",
        "onlyfew" => "Das wird nur wenige Augenblicke dauern.",
        "download_finished" => "Download abgeschlossen",
        "success" => "Herunterladen und Entpacken erfolgreich abgeschlossen!",
        "launchi" => "Einrichtungsassistenten starten",
        "syscheck" => "Kurzer Servercheck",
        "mem_label" => "Arbeitsspeicher",
        "mem_warn" => "Der Arbeitsspeicher ist etwas knapp bemessen. Daher könnten gelegentlich komplexe Seiten nicht vollständig generiert und stattdessen leer ausgegeben werden. Es wäre sinnvoll, die PHP-Einstellung &quot;memory_limit&quot; auf 256 MB oder höher zu setzen.",
        "mem_error" => "Der Arbeitsspeicher ist zu gering. Das ePartool wird daher oft keine Seiten generieren können. Die PHP-Einstellung &quot;memory_limit&quot; sollte auf 256 MB oder höher gesetzt werden.",
        "mem_tmp" => "Der Downloader konnte vorläufig mehr Arbeitsspeicher vom Server anfordern. Leider ist das nur eine vorläufige Lösung und wird nur für die nächsten Minuten funktionieren.",
        "mem_use" => "Aktueller Arbeitsspeicherverbrauch",
        "php_version" => "PHP-Version",
        "php_warn" => "Die PHP-Version ist etwas veraltet. Das ePartool wird auf eine Kompatibilität nicht mehr getestet. Wir empfehlen eine Aktualisierung auf Version 7.1.",
        "php_error" => "Die PHP-Version ist leider zu alt. Daher kann die Installation nicht fortgeführt werden. Bitte auf PHP 7.1 aktualisieren und dann dieses Skript erneut starten.",
        "time_label" => "Erlaubte Ausführungsdauer",
        "time_warn" => "Die zugelassene Ausführungsdauer ist ein bisschen knapp. Je nach Rechengeschwindigkeit des Servers könnten ab und zu Fehler passieren und leere Seiten angezeigt werden. Es wäre möglicherweise sinnvoll die PHP-Einstellung &quot;max_execution_time&quot; auf 60 Sekunden oder länger zu setzen.",
        "time_error" => "Die zugelassene Ausführungsdauer für PHP-Skripte ist zu kurz. Das ePartool wird gelegentlich leere Seiten produzieren. Die PHP-Einstellung &quot;max_execution_time&quot; sollte am besten auf 60 Sekunden oder länger gesetzt werden.",
        "time_tmp" => "Das Downloadskript konnte erfolgreich mehr Ausführungszeit vom Server zugelassen bekommen. Leider ist dies nur eine vorübergehende Lösung für die kommenden Minuten.",
        "enc_label" => "Verschlüsselung",
        "enc_off" => "Aus",
        "enc_on" => "An",
        "enc_warn" => "Wenn das ePartool ohne aktiver Verschlüsselung installiert und genutzt wird, werden alle Passwörter und sonstige Eingaben offen übertragen, ähnlich leicht einsehbar wie eine Postkarte. Die Daten können von Dritten jederzeit mitgelesen werden. Aus Datenschutzgründen werden alle ortsbasierten Funktionen (Landkarten und eigener Standort) deaktiviert sein. Wir empfehlen dringend eine verschlüsselte HTTPS-Verbindung statt des offenen HTTPS einzusetzen. Dafür benötigt der Server die Installation eines sogenannten SSL-Zertifikats.",
        "enc_off_cont" => "Vorerst ohne Verschlüsselung weitermachen",
        "enc_on_cont" => "Zur verschlüsselten HTTPS-Verbindung wechseln",
        "sec" => "Sekunden",
        "fail" => "Ein Fehler ist aufgetreten :(",
        "sorry_failed" => "Beim Download und Entpacken ist ein Fehler aufgetreten. Das tut uns leid.</p><p>Hilfe gibt's auf der <a href=\"https://tooldoku.dbjr.de\" target=\"_blank\">ePartool-Website</a> oder bei <a href=\"mailto:digital@dbjr.de\">digital@dbjr.de</a>."
        );
    
    } else {
        
        $txt = array(
        "autodown" => "Automatic Downloader",
        "btn_begin_download" => "Begin download",
        "downloading" => "Downloading",
        "plswait" => "Please wait while downloading and extracting the ePartool.",
        "onlyfew" => "This will take a few moments only.",
        "download_finished" => "Download finished",
        "success" => "Download and extraction successful!",
        "launchi" => "Launch configuration wizard",
        "syscheck" => "Checking the server requirements",
        "mem_label" => "PHP memory limit",
        "mem_warn" => "The memory limit is a bit low. You may encounter errors with empty pages. Consider updating the &quot;memory_limit&quot; setting to 256 MB or higher.",
        "mem_error" => "The memory limit is too low. The ePartool will often run into problems, as a result you will see empty pages instead of expected content. The &quot;memory_limit&quot; should be 256 MB or higher.",
        "mem_tmp" => "The downloader script was able to successfully request more memory from the server. Unfortunately this is only a temporary solution and will work only for the next minutes.",
        "mem_use" => "Aktueller Arbeitsspeicherverbrauch",
        "php_version" => "PHP version",
        "php_warn" => "Your PHP version is a bit outdated. The ePartool is not tested on this version anymore. We recommend updating PHP to version 7.1",
        "php_error" => "Your PHP version is very old. Unfortunately you won't be able to install the ePartool. Please update PHP to version 7.1 and try again.",
        "time_label" => "Max. execution time for PHP scripts",
        "time_warn" => "The allowed execution time is a bit short. In rare cases you may encounter errors with empty pages. Consider updating the &quot;max_execution_time&quot; to 60 seconds or longer.",
        "time_error" => "The allowed execution time is too short. The ePartool will often fail and only show empty pages. &quot;max_execution_time&quot; should be set to 60 seconds or longer.",
        "time_tmp" => "The downloader script was able to successfully request more execution time from the server. Unfortunately this is only a temporary solution and will work only for the upcoming minutes.",
        "enc_label" => "Encryption",
        "enc_off" => "Off",
        "enc_on" => "On",
        "enc_warn" => "If the ePartool is installed and used without encryption on, all your passwords and user input will be transmitted totally open, like a postcard. Anyone can read your data. As a matter of privacy, any location-based service (maps and my location) will be disabled. We strongly recommend to use a encrypted HTTPS conncection instead of HTTP. This will only work if your server has a so-called SSL certificate installed.",
        "enc_off_cont" => "Continue without encryption",
        "enc_on_cont" => "Switch to encrypted HTTPS connection",
        "sec" => "seconds",
        "fail" => "An error occurred :(",
        "sorry_failed" => "Sorry, but something went wrong while downloading and decrompressing the installation package.</p><p>Please check the <a href=\"https://tooldoku.dbjr.de\" target=\"_blank\">ePartool website</a> for updated information or contact <a href=\"mailto:digital@dbjr.de\">digital@dbjr.de</a>."
        );

        
}

?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
body {
  color: #555;
  margin: 0 auto;
  font-size: 16pt;
  font-family: sans-serif;
  line-height: 1.4;
}
a {
  color: #53928e;
  text-decoration: none;
}
a:hover {
  text-decoration: underline;
}
.section-primary {
  background-color: #53928e;
  height: 140px;
  padding: 51px 0 0 62px;
}
.section-content {
  padding: 62px;
}
h1 {
  color: #fff;
  font-size: 22pt;
  font-weight: 300;
  margin-top: 8px;
  margin-bottom: 0px;
}
h2 {
  color: #333;
  font-size: 22pt;
  font-weight: 300;
  margin-top: 8px;
  margin-bottom: 10px;
}
img {
  padding: 0;
}
.spinner {
  box-sizing: border-box;
  height: 60px;
  width: 60px;
  margin: 80px;
  border: 0px solid #eee;
  border-radius: 50%;
  box-shadow: 0 -20px 0 24px #eee inset;
  animation: rotate 1s infinite linear;
}
@keyframes rotate {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
.warning {
  color: orange;
  font-size: smaller;
}
.error {
  color: red;
  font-size: smaller;
}
.ok {
  color: green;
  font-size: smaller;
}
button {
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: #f2f2f2;
  cursor: pointer;
}

footer {
  position: fixed;
  bottom: 0px;
  background-color: #ccc;
  padding: 6px 50px;
  color: #fff;
  font-size: 13pt;
  width: 100%;
}
footer a {
  color: #fff;
  text-decoration: none;
}
footer a:hover {
  color: #53928e;
  text-decoration: none;
}
</style>
<title>ePartool Download</title>
<link rel="icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAolJREFUeNpiYBhgwIgu8FpKMQFI+QOxAxALIEl9AOILUDaIfgiiRZ/dP0A1BwAtnw+kEsgwB+QgkEM2kuogRjSfz6dCqIJCagEQLwQ65gIpDniPFuTUAKDQmAh0yAa8DqCi7/FFUSG26GGC0v6kmMYsK8PA5uHKwFWcD6YZ+fgIaTEA4v1Aj/bjCoH/RCUYoEV882YysFqao4j///SJ4Wt9M8OPVWuJihZgSDjCzQRaDspu+4nRKbBrCwOLthac/+/xEwYmYGjAwHsLO4a/QDEiwASgIwphUWBAjA6OsGC45T+BPn0jrcTwDmjhBzcfhJqURGJjsQDqcbADiEr5XEX58OD+AgxuGPhz9RoYgwB61BAA8TAH2BOT6GBB/WvHbrAjUNPAZzCNHD1EAAfkXIAXIPvs9/ETGPKwECARKBDtAOSE9vfxUwx5En2OtRwgmP3wy/PCcwWpgIUoRUg+5G6sQYpzTRTH/X3ylDYOICa4QQkTW/qgugM+Jacz/P/4GVEYPXlCbOGDrdYEO+AgLEvgAuC4heYEkOXk+BRHBUVcIvz75AkDDcBGmAMItmCQ8zmo9qMS2ABzwANCKn8fO4m1TkBPnCQ4DlQjPkCujgm2hnj7uxnYgZYjSkSEo5hlpMGFFUjsY0gkMQ5whDVOWJCaTgH4dIAqIGZgvof5HlvFQ2SRfAC5ZURSkwxU6HCmJgItt4A7AORrUC4BNUaIyB2grGcIC370Rul+QtmRCiAQvYGKnA0DkToetAL+OCsjoMtAwQNqqzXCSikagARo5wd31wwpShyQokSfhD6DABHNvAVADyfidQClAOgBA2ijwx7qIAdsjmBkoCOAOsoBGqIGxJTCNAcAAQYAfH/oKEf4j3IAAAAASUVORK5CYII=" />
</head>
<body>
<section class="section-primary">
<img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjxzdmcKICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIgogICB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiCiAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgaWQ9InN2ZzgiCiAgIHZlcnNpb249IjEuMSIKICAgdmlld0JveD0iMCAwIDEwMCAyNSIKICAgaGVpZ2h0PSIyNSIKICAgd2lkdGg9IjEwMCI+CiAgPG1ldGFkYXRhCiAgICAgaWQ9Im1ldGFkYXRhMTQiPgogICAgPHJkZjpSREY+CiAgICAgIDxjYzpXb3JrCiAgICAgICAgIHJkZjphYm91dD0iIj4KICAgICAgICA8ZGM6Zm9ybWF0PmltYWdlL3N2Zyt4bWw8L2RjOmZvcm1hdD4KICAgICAgICA8ZGM6dHlwZQogICAgICAgICAgIHJkZjpyZXNvdXJjZT0iaHR0cDovL3B1cmwub3JnL2RjL2RjbWl0eXBlL1N0aWxsSW1hZ2UiIC8+CiAgICAgICAgPGRjOnRpdGxlPjwvZGM6dGl0bGU+CiAgICAgIDwvY2M6V29yaz4KICAgIDwvcmRmOlJERj4KICA8L21ldGFkYXRhPgogIDxkZWZzCiAgICAgaWQ9ImRlZnMxMiIgLz4KICA8ZwogICAgIGZpbGw9IiNGRkYiCiAgICAgaWQ9Imc2Ij4KICAgIDxwYXRoCiAgICAgICBpZD0icGF0aDIiCiAgICAgICBkPSJNMTkuMDE4IDE3LjYzNmMuNTExLS4zNDUgMi43NjctMy4xNDQgMy4wMDQtNi44NDEuMzY3LTQuMDc2LS41MjQtNi4yNjctNC44MS04LjI1OEMxMS41ODMtLjEzOSA0Ljg5Mi0uMzExIDMuNjI0LjI3N2MwIDAtMS45NC4yNi0yLjk1OSA0Ljc1LS40MTkgMS44NDUtLjkxNCA1LjA3Mi4xOTEgMTAuMzk1Ljg2NyA0LjE3NyA1LjE0NSA0LjAxNyA1LjM5OSA0LjA2My44NTMuMTU1IDUuNDU2LjExOSA3LjMyNi4xMTkgMi4yNTUgMCA3Ljk2NiAxLjU5OSA5LjAwNiAxLjU5OWwtMy41NjktMy41Njd6bS00Ljc3NS04YTExLjEyOCAxMS4xMjggMCAwIDEtLjIxMiAxLjU3OWMtLjQ0LjA4My0uOTA3LjE1Mi0xLjQwMS4yMDYtLjQ5NC4wNTQtLjk4NC4wOTgtMS40Ny4xMzEtLjQ4Ni4wMzQtLjk0My4wNTQtMS4zNy4wNjMtLjQyNy4wMDgtLjc5MS4wMTMtMS4wOS4wMTMuMDE3LjY4Mi4wNzkgMS4yNDYuMTg3IDEuNjkxLjEwNy40NDYuMjU1Ljc5NS40NDEgMS4wNDlhMS41IDEuNSAwIDAgMCAuNzE1LjUzN2MuMjkuMTA0LjYzMS4xNTYgMS4wMi4xNTYuNTA3IDAgMS4wMDgtLjA3MiAxLjUwMy0uMjE4YTYuOTgyIDYuOTgyIDAgMCAwIDEuNDAyLS41ODFjLjA2Ni4xOTIuMTE0LjQyMy4xNDMuNjkzLjAyOS4yNy4wNDQuNTEuMDQ0LjcxOCAwIC4yMDgtLjA5NC4zOTUtLjI4MS41NjItLjE4Ny4xNjctLjQ0Mi4zMTQtLjc2Ni40NDNhNS4yNTcgNS4yNTcgMCAwIDEtMS4xMjIuMjk0Yy0uNDIzLjA2Ni0uODc2LjEtMS4zNTguMWE1Ljc1NSA1Ljc1NSAwIDAgMS0xLjY1NS0uMjI1IDIuNjI3IDIuNjI3IDAgMCAxLTEuMjkyLS44OGMtLjM2LS40MzctLjY0NC0xLjA0Ny0uODUzLTEuODI5LS4yMDktLjc4Mi0uMzE0LTEuNzk4LS4zMTQtMy4wNDYgMC0xLjYwNi4xMzQtMi44OS40MDEtMy44NTIuMjY3LS45NjEuNjEtMS42OTggMS4wMjctMi4yMS40MTgtLjUxMi45LS44NDcgMS40NDctMS4wMDVhNi4wMDEgNi4wMDEgMCAwIDEgMS42NzMtLjIzN2MyLjE2MSAwIDMuMjQyIDEuMzY5IDMuMjQyIDQuMTA3LjAwMS41NjYtLjAyIDEuMTQ2LS4wNjEgMS43NDF6IiAvPgogICAgPHBhdGgKICAgICAgIGlkPSJwYXRoNCIKICAgICAgIGQ9Ik0xMC44NCA1LjU5N2MtLjI1OCAwLS41MDYuMDY1LS43NDcuMTk0cy0uNDU5LjM2LS42NTMuNjkzYy0uMTk1LjMzMy0uMzU1Ljc3Ni0uNDc5IDEuMzI5LS4xMjUuNTU0LS4yMDMgMS4yNjQtLjIzNiAyLjEyOWguODE0Yy4zMDMgMCAuNjEyLS4wMTMuOTI3LS4wMzguMzE2LS4wMjUuNjItLjA1NC45MTUtLjA4OGE0LjQ4IDQuNDggMCAwIDAgLjc3OC0uMTVjLjA1LS4yNDEuMDgzLS40OTkuMDk5LS43NzQuMDE3LS4yNzUuMDI1LS41MjkuMDI1LS43NjItLjAxNy0uOTI0LS4xMjUtMS41NzctLjMyNC0xLjk2LS4xOTktLjM4MS0uNTcyLS41NzMtMS4xMTktLjU3M3pNMjYuMTEzIDI0LjczNlY0LjE5M2MwLS4zNDIuMTcyLS41MTQuNTE2LS41MTRoMi45OTVjMS45MTggMCAzLjMyMy40NjcgNC4yMTMgMS40MDEuODkuOTM0IDEuMzM2IDIuNDUxIDEuMzM2IDQuNTUzIDAgMS4yNDgtLjE0NiAyLjI4OS0uNDM2IDMuMTI0LS4yOTEuODM1LS42OTIgMS41MDgtMS4yMDUgMi4wMTlhNC40NTMgNC40NTMgMCAwIDEtMS43ODkgMS4wOThjLS42OC4yMjEtMS4zOS4zMzItMi4xMjguMzMyaC0xLjA2NHY4LjA0OGEuNzkyLjc5MiAwIDAgMS0uMDQ0LjI4NmMtLjAyOS4wNzQtLjEyLjEyNS0uMjcyLjE1NGE0LjIyMiA0LjIyMiAwIDAgMS0uNzE5LjA0NGMtLjMyOS0uMDAyLS43OTYtLjAwMi0xLjQwMy0uMDAyem0yLjQzOC0xMC43OTNoMS4zMjFjLjg0MiAwIDEuNDc4LS4zNDggMS45MDktMS4wNDQuNDMxLS42OTYuNjQ2LTEuODc4LjY0Ni0zLjU0NSAwLTEuMDY5LS4xNzYtMS45MjctLjUyOS0yLjU3NC0uMzUzLS42NDctLjk2OS0uOTcxLTEuODUtLjk3MUgyOC41NXY4LjEzNHpNMzMuMzI0IDI0LjcwNmw1LjM1OS0yMC42ODljLjA1OS0uMjg0LjM2Ny0uNDI2LjkyNS0uNDI2LjQ2IDAgLjg0Mi4wMjkgMS4xNDUuMDg4bDUuMzg5IDIwLjZhLjQyLjQyIDAgMCAxIC4wMTUuMTAzYzAgLjA5OC0uMDQ5LjE4Ni0uMTQ3LjI2NS0uMTM3LjA5OC0uNDQ2LjE0Ny0uOTI1LjE0N2gtLjYzMWMtLjI0NSAwLS41MTQtLjAyLS44MDctLjA1OWwtMS4yOTItNS4zNzRoLTUuMTgzbC0xLjE2IDQuOTQ3YS41OTUuNTk1IDAgMCAxLS4yNDIuMzYxYy0uMTIzLjA4My0uNDI0LjEyNS0uOTAzLjEyNS0uMTU3IDAtLjM4Mi0uMDA1LS42NzYtLjAxNWE4LjE3MSA4LjE3MSAwIDAgMS0uODY3LS4wNzN6bTQuMzYxLTcuNTQ3aDQuMTU2bC0xLjAyOC00LjI3M2E3OS42MyA3OS42MyAwIDAgMS0uMzQ1LTEuNTM1IDM4LjI4NSAzOC4yODUgMCAwIDEtLjMwMS0xLjU0OSA2MC43MzIgNjAuNzMyIDAgMCAxLS4yMzYtMS40NTMgMjguNDUgMjguNDUgMCAwIDEtLjE2MS0xLjIzM2MtLjAzOS4zNjItLjEuNzczLS4xODQgMS4yMzMtLjA4My40Ni0uMTY5Ljk0LS4yNTcgMS40MzlhMTA4Ljg4IDEwOC44OCAwIDAgMS0uMjg2IDEuNTQ5Yy0uMTAzLjUzNC0uMjEzIDEuMDUtLjMzIDEuNTQ5bC0xLjAyOCA0LjI3M3pNNDcuMTQ1IDI0LjczNlY0LjE5M2MwLS4wNzguMDE5LS4xNTYuMDU5LS4yMzUuMDM5LS4wNTkuMDkxLS4xMi4xNTUtLjE4NHMuMTY1LS4wOTYuMzAyLS4wOTZoMi45OTVjMS45MTggMCAzLjMyMy40NjcgNC4yMTMgMS40MDEuODkuOTM0IDEuMzM2IDIuNDUxIDEuMzM2IDQuNTUzIDAgMS42My0uMjQyIDIuOTExLS43MjUgMy44NDQtLjQ4My45MzMtMS4xMTkgMS42Mi0xLjkwNyAyLjA2Mi4yNDYuMzAzLjUxNS43MDQuODA2IDEuMjAzLjI5MS40OTkuNTc5IDEuMS44NjUgMS44MDQuMjg2LjcwNC41MzUgMS41MjUuNzQ3IDIuNDY0LjIxMi45MzkuMzU3IDIgLjQzNSAzLjE4MyAwIC4yMjUtLjA2MS4zODEtLjE4Mi40Ny0uMTIxLjA4OC0uNDY4LjEzMi0xLjA0LjEzMi0uMTk0IDAtLjQxMS0uMDA1LS42NTMtLjAxNS0uMjQyLS4wMS0uNDk4LS4wMjQtLjc2OS0uMDQ0YTI1LjYxNiAyNS42MTYgMCAwIDAtLjQ2NS0zLjMzNSAyMC44IDIwLjggMCAwIDAtLjY3OS0yLjQ3N2MtLjI0NC0uNjg5LS40OTMtMS4yNTgtLjc0Ni0xLjcwOC0uMjU0LS40NDktLjQ3LS43OTYtLjY0OS0xLjA0MS0uMDk4LjAyLS4xOTguMDI5LS4zMDEuMDI5SDQ5LjU4NHY4LjA0OGEuNzkyLjc5MiAwIDAgMS0uMDQ0LjI4NmMtLjAyOS4wNzQtLjEyLjEyNS0uMjcyLjE1NGE0LjIyMiA0LjIyMiAwIDAgMS0uNzE5LjA0NGwtMS40MDQuMDAxem0yLjQzOC0xMC43OTNoMS4zMjFjLjg0MiAwIDEuNDc4LS4zMzYgMS45MDktMS4wMDguNDMxLS42NzIuNjQ2LTEuODEyLjY0Ni0zLjQyIDAtMS4xNDctLjE2OS0yLjA1Mi0uNTA3LTIuNzE0LS4zMzgtLjY2Mi0uOTYyLS45OTMtMS44NzItLjk5M2gtMS40OTh2OC4xMzV6TTU2LjM5IDUuOTQxVjQuMTg2YzAtLjMzOC4xNzEtLjUwNy41MTQtLjUwN2gxMC4yMzV2MS43ODNjMCAuMzE5LS4xNzEuNDc4LS41MTQuNDc4aC0zLjd2MTguMzFhLjc5NC43OTQgMCAwIDEtLjA0NC4yODdjLS4wMjkuMDc0LS4xMi4xMjUtLjI3MS4xNTVhNC4yOTEgNC4yOTEgMCAwIDEtLjcyNy4wNDRoLTEuMzk1VjUuOTQxSDU2LjM5ek02Ni42MyAxNC4wOWMwLTIuMDA3LjExNy0zLjY5LjM1Mi01LjA1MS4yMzUtMS4zNjEuNTkyLTIuNDU3IDEuMDcyLTMuMjg5czEuMDc5LTEuNDI5IDEuNzk5LTEuNzkxYy43MTktLjM2MiAxLjU2NC0uNTQzIDIuNTMzLS41NDMuOTc5IDAgMS44MDguMTcxIDIuNDg5LjUxNC42OC4zNDMgMS4yMzEuOTE4IDEuNjUyIDEuNzI1LjQyMS44MDcuNzI3IDEuODk0LjkxOCAzLjI2LjE5MSAxLjM2Ni4yODYgMy4wNjIuMjg2IDUuMDg4IDAgMi4wNjYtLjExIDMuODAxLS4zMyA1LjIwNS0uMjIgMS40MDUtLjU2IDIuNTM4LTEuMDIgMy4zOTktLjQ2Ljg2MS0xLjA1MiAxLjQ3Ni0xLjc3NyAxLjg0My0uNzI1LjM2Ni0xLjU3Ny41NS0yLjU1Ni41NS0uOTYgMC0xLjc4Ny0uMTU5LTIuNDgyLS40NzctLjY5NS0uMzE4LTEuMjU4LS44OTMtMS42ODgtMS43MjUtLjQzMS0uODMyLS43NDctMS45NS0uOTQ3LTMuMzU1LS4yMDEtMS40MDYtLjMwMS0zLjE4OS0uMzAxLTUuMzUzem0yLjc0Ni42MDJjMCAxLjQuMDQ0IDIuNTkyLjEzMiAzLjU3Ni4wODguOTg0LjI0MiAxLjc5MS40NjMgMi40MjMuMjIuNjMxLjUwNiAxLjA5Mi44NTkgMS4zOC4zNTIuMjg5Ljc5OC40MzMgMS4zMzYuNDMzcy45OTQtLjE0OSAxLjM2Ni0uNDQ4Yy4zNzItLjI5OS42NzMtLjc4OC45MDMtMS40NjkuMjMtLjY4LjM5Ni0xLjU1MS40OTktMi42MTQuMTAzLTEuMDYyLjE1NC0yLjM2Ni4xNTQtMy45MTMgMC0xLjUwNy0uMDQ2LTIuNzc3LS4xNC0zLjgxLS4wOTMtMS4wMzMtLjI0NS0xLjg3NS0uNDU1LTIuNTI2LS4yMS0uNjUxLS40OS0xLjExNi0uODM3LTEuMzk1LS4zNDgtLjI3OS0uNzkxLS40MTktMS4zMjktLjQxOS0uNTE5IDAtLjk2Ny4xNTQtMS4zNDQuNDYyLS4zNzcuMzA4LS42ODUuODEtLjkyNSAxLjUwNS0uMjQuNjk1LS40MTQgMS41OTgtLjUyMSAyLjcwOS0uMTA3IDEuMTEzLS4xNjEgMi40ODEtLjE2MSA0LjEwNnpNNzguOTIyIDE0LjA5YzAtMi4wMDcuMTE3LTMuNjkuMzUyLTUuMDUxLjIzNS0xLjM2MS41OTItMi40NTcgMS4wNzItMy4yODkuNDgtLjgzMiAxLjA3OS0xLjQyOSAxLjc5OS0xLjc5MS43MTktLjM2MiAxLjU2NC0uNTQzIDIuNTMzLS41NDMuOTc5IDAgMS44MDguMTcxIDIuNDg5LjUxNC42OC4zNDMgMS4yMzEuOTE4IDEuNjUyIDEuNzI1LjQyMS44MDcuNzI3IDEuODk0LjkxOCAzLjI2LjE5MSAxLjM2Ni4yODYgMy4wNjIuMjg2IDUuMDg4IDAgMi4wNjYtLjExIDMuODAxLS4zMyA1LjIwNS0uMjIgMS40MDUtLjU2IDIuNTM4LTEuMDIgMy4zOTktLjQ2Ljg2MS0xLjA1MyAxLjQ3Ni0xLjc3NyAxLjg0My0uNzI2LjM2Ni0xLjU3Ny41NS0yLjU1Ni41NS0uOTYgMC0xLjc4Ny0uMTU5LTIuNDgyLS40NzctLjY5NS0uMzE4LTEuMjU4LS44OTMtMS42ODgtMS43MjUtLjQzMS0uODMyLS43NDctMS45NS0uOTQ3LTMuMzU1LS4yMDEtMS40MDYtLjMwMS0zLjE4OS0uMzAxLTUuMzUzem0yLjc0NS42MDJjMCAxLjQuMDQ0IDIuNTkyLjEzMiAzLjU3Ni4wODguOTg0LjI0MiAxLjc5MS40NjMgMi40MjMuMjIuNjMxLjUwNiAxLjA5Mi44NTkgMS4zOC4zNTIuMjg5Ljc5OC40MzMgMS4zMzYuNDMzcy45OTQtLjE0OSAxLjM2Ni0uNDQ4Yy4zNzItLjI5OS42NzMtLjc4OC45MDMtMS40NjkuMjMtLjY4LjM5Ni0xLjU1MS40OTktMi42MTQuMTAzLTEuMDYyLjE1NC0yLjM2Ni4xNTQtMy45MTMgMC0xLjUwNy0uMDQ2LTIuNzc3LS4xNC0zLjgxLS4wOTMtMS4wMzMtLjI0NS0xLjg3NS0uNDU1LTIuNTI2LS4yMS0uNjUxLS40OS0xLjExNi0uODM3LTEuMzk1LS4zNDgtLjI3OS0uNzkxLS40MTktMS4zMjktLjQxOS0uNTE5IDAtLjk2Ny4xNTQtMS4zNDMuNDYyLS4zNzcuMzA4LS42ODUuODEtLjkyNSAxLjUwNS0uMjQuNjk1LS40MTQgMS41OTgtLjUyMSAyLjcwOS0uMTA4IDEuMTEzLS4xNjIgMi40ODEtLjE2MiA0LjEwNnpNOTIuMTgxIDI0LjczNlY0LjM4NGMwLS4xNzYuMDE0LS4zMTEuMDQzLS40MDQuMDI5LS4wOTMuMTIzLS4xNjEuMjgzLS4yMDUuMTYtLjA0NC40MDQtLjA3MS43MzMtLjA4MS4zMjktLjAxLjc4OC0uMDE1IDEuMzc5LS4wMTV2MTguNzk1aDUuMTk4djEuNzgzYzAgLjE5NC0uMDQ0LjMyMi0uMTMyLjM4NC0uMDg4LjA2My0uMjE2LjA5NC0uMzgyLjA5NGgtNy4xMjJ6IiAvPgogIDwvZz4KPC9zdmc+Cg==" alt="ePartool logo" title="Logo of the ePartool" /><br>
<h1><?= $txt["autodown"] ?></h1>
</section><section class="section-content"><?php

if ($_SERVER['QUERY_STRING'] == "download")  {

    /* Download is launched */

    ?>
    <h2><?= $txt["downloading"] ?></h2>
    <p>
        <?= $txt["plswait"] ?><br>
        <?= $txt["onlyfew"] ?>

    </p><div class="spinner"></div>
        
    <?php
    
    // Get latest ePartool file
    
    $ch = curl_init();
    $source = "https://tooldoku.dbjr.de/external/epartool-latest.zip"; // Where the installer package is retrieved from

    curl_setopt($ch, CURLOPT_URL, $source);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $data = curl_exec ($ch);
    curl_close ($ch);

    // Save as epartool.zip
    $destination = "epartool.zip"; // File saved as epartool.zip
    $file = fopen($destination, "w+");
    fputs($file, $data);
    fclose($file);

    // Unzip
    $zip = new ZipArchive;
    $res = $zip->open('epartool.zip');
    if ($res === TRUE) {
        $zip->extractTo('.'); // extract to very same directory
        $zip->close();
        
        unlink('epartool.zip'); // delete zip
        
    echo "<br>" . $txt["mem_use"] . ': ' . round((memory_get_peak_usage()/1048576), 2) . ' MB / ' . ini_get('memory_limit'). "<br>\n"; 
        

    } else {

        // If unzip failed

        echo "<script>self.location.href = '" . $_SERVER["PHP_SELF"] . "?fail';</script>";

    }

} elseif ($_SERVER['QUERY_STRING'] == "download-run")  {

    /* Download and decompression was successful, now offer to launch configuration wizard */
    //TODO: now in place .htaccess actually obstructs showing this section, but moves on to configuration wizard already
    
    ?>
    <h2><?= $txt["download_finished"] ?></h2>
    <p>
        <?= $txt["success"] ?><br>
        
    </p><p>
        <a href="./install/"><button><?= $txt['launchi'] ?></button></a>
    </p>
    <?php

        unlink('epartool-downloader.php');  // this script is deleted from server for security reasons

} elseif ($_SERVER['QUERY_STRING'] == "fail")  {
    
    /* If download and decompression failed somehow */
    
    ?>
    <h2><?= $txt["fail"] ?></h2>
    <p>
        <?= $txt["sorry_failed"] ?>
    </p>
    <?php
    
    
} else {

    /* First step is a small server check */


    echo '<h2>' . $txt["syscheck"] . '</h2>';
    
    
    /* Check PHP version */
    
    echo '<p>' . $txt["php_version"] . ': ' . (float)phpversion() . ' ... ';

    if (version_compare(phpversion(), '7.2.0', '<')) {
        
        echo '<span class="error">' . $txt["php_error"] . '</span><br />'; // PHP version is too old

        $stopper = "yes";
        
    } elseif (version_compare(phpversion(), '7.3.0', '<')) {
        
        echo '<span class="warning">' . $txt["php_warn"] . '</span><br />'; // PHP version is a bit outdated
        
    } else {
        
        echo '<span class="ok">OK</span><br />';

    }
    echo '</p>';


    
    /* Check memory limit */
    
    echo '<p>' . $txt["mem_label"] . ': ' . $memory . 'B ... ';

    if ($memory < "128M") { 
        
            echo '<span class="error">' . $txt["mem_error"] . '</span><br />'; // Available memory too low, TODO evaulate whether stopper should be necessary
            
            if (ini_get('memory_limit') > "127M") {
            
                echo '<span class="warning">' . $txt["mem_tmp"] . '</span><br />'; // Inform about temporary solution of memory limit increase
            }
            
        
        } elseif ($memory < "256M") { 
        
            echo '<span class="warning">' . $txt["mem_warn"] . '</span><br />'; // Available memory a bit low
            
            if (ini_get('memory_limit') > "255M") {
            
                echo '<span class="warning">' . $txt["mem_tmp"] . '</span><br />'; // Inform about temporary solution of memory limit increase
            }
        
        } else { 

            echo '<span class="ok">OK</span><br />';

        }
    echo '</p>';
            
    /* Check max execution time */

    echo '<p>' . $txt["time_label"] . ': ' . ini_get('max_execution_time') . ' ' . $txt["sec"] . ' ... ';

    if (ini_get('max_execution_time') < "40") { 
        
            echo '<span class="error">' . $txt["time_error"] . '</span><br />'; // Available execution time very low, TODO evaulate whether stopper should be necessary
        
        } elseif (ini_get('max_execution_time') < "60") { 
        
            echo '<span class="warning">' . $txt["time_warn"] . '</span><br />'; // Available execution time a bit low
        
        } else { 

            echo '<span class="ok">OK</span><br />';

        }
    echo '</p>';


    /* Check encryption */

    echo '<p>' . $txt["enc_label"] . ': ';


    if (isset($_SERVER['HTTPS']) || $_SERVER['SERVER_PORT'] == 443) {

        echo '<span class="ok">' . $txt["enc_on"] . '</span><br />';
        $security = "okay";
        
    } else {

        echo '<span class="error">' . $txt["enc_off"] . '</span><br />';
        
        if ($security != "okay") {
            
            echo '<span class="error">' . $txt["enc_warn"] . '</span></p>';
            
            // Let user choose whether to continue with unsecure http or switch to https (no availability check made though)
            
            echo '<a href="https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '"><button>' . $txt['enc_on_cont'] . '</button></a> &nbsp;';
            echo '<a href="http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] .'?encryption=off"><button>' . $txt['enc_off_cont'] . '</button></a>';
        }
        
        echo "</p>";
    }

    if ($stopper == "no") {

        if ($security == "okay") {

            // Show button to begin download if there is no stopper so far
            
            echo'<br><a href="' . $_SERVER["PHP_SELF"] . '?download"><button>' . $txt['btn_begin_download'] . '</button></a>';


        }
    } // end of stopper == no
}
?>
</section><footer><a href="https://tooldoku.dbjr.de" target="_blank">➤ ePartool-Website</a> &nbsp; ➤ <?= $txt["autodown"].' v'.constant("EPT_DL_VERSION") ?></footer>
</body></html>
