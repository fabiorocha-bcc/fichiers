<?php
$arquivo = base64_decode($_GET['arquivo']);
 if(isset($arquivo) && file_exists($arquivo)){ // faz o teste se a variavel não esta vazia e se o arquivo realmente existe
      switch(strtolower(pathinfo($arquivo, PATHINFO_EXTENSION))){ // verifica a extensão do arquivo para pegar o tipo
         case "pdf": $tipo="application/pdf"; break;
         case "exe": $tipo="application/octet-stream"; break;
         case "zip": $tipo="application/zip"; break;
         case "doc": $tipo="application/msword"; break;
         case "docx": $tipo="application/vnd.openxmlformats-officedocument.wordprocessingml.document"; break;         
         case "xls": $tipo="application/vnd.ms-excel"; break;
         case "xlsx": $tipo="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"; break;
         case "odp": $tipo="application/vnd.oasis.opendocument.presentation"; break;
         case "ods": $tipo="application/vnd.oasis.opendocument.spreadsheet"; break;
         case "odt": $tipo="application/vnd.oasis.opendocument.text"; break;
         case "ppt": $tipo="application/vnd.ms-powerpoint"; break;
         case "pps": $tipo="application/vnd.ms-powerpoint"; break;
         case "pptx": $tipo="application/vnd.openxmlformats-officedocument.presentationml.presentation"; break;
         case "ppsx": $tipo="application/vnd.openxmlformats-officedocument.presentationml.slideshow"; break;
         case "gif": $tipo="image/gif"; break;
         case "png": $tipo="image/png"; break;
         case "jpg": $tipo="image/jpg"; break;
         case "mp3": $tipo="audio/mpeg"; break;
         case "php": // deixar vazio por seurança
         case "htm": // deixar vazio por seurança
         case "html": // deixar vazio por seurança
      }
      header("Content-Type: ".$tipo); // informa o tipo do arquivo ao navegador
      header("Content-Length: ".filesize($arquivo)); // informa o tamanho do arquivo ao navegador
      header("Content-Disposition: attachment; filename=".basename($arquivo)); // informa ao navegador que é tipo anexo e faz abrir a janela de download, tambem informa o nome do arquivo
      readfile($arquivo); // lê o arquivo
      exit; // aborta pós-ações
   }
   else 
       echo "FILE NOT FOUND " .$arquivo;
?>