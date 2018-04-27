<?php

// no direct access
defined('_JEXEC') or die;

class plgContentFichiers extends JPlugin {
    /*
     * CONSTANTES
     */

    const PLG_NAME = 'fichiers';                     // NOME DO PLUGIN
    const PLG_PARAM = 'fichiers';                   // PARAMETRO CONTIDO NO ARTIGO PARA INICIAR PLUGIN. case-sensitive
    const PLG_PATH = '/plugins/content/fichiers';    // DIRETORIO PADRÃO INSTALAÇÃO PLUGIN JOOMLA 3.X
    const PLG_IMG_PATH = '/plugins/content/fichiers/img/';    // DIRETORIO PADRÃO INSTALAÇÃO PLUGIN JOOMLA 3.X
    const PLG_STYLE = '/css/default.css';           // ARQUIVO DE ESTILO PLUGIN
    const FC_STYLE = '/css/jquery.fancybox.css';    // ARQUIVO DE ESTILO PLUGIN
    const PLG_JS = '/js/fichiers.js';                // ARQUIVO SCRIPT JQUERY PARA PLUGIN
    const FC_JS = '/js/jquery.fancybox.js';                // ARQUIVO SCRIPT JQUERY PARA PLUGIN
    const PLG_CFG = '/fichiers.ini';                // ARQUIVO COM AS DEFINIÇÕES E DETALHES DOS ARQUIVOS EXISTENTES NO DIRETÓRIO

    /*
     * ATRIBUTOS
     */

    private $plgFileFolder;                         // DIRETÓRIO RAIZ DOS DIRETORIOS A SEREM LISTADOS PELO PLUGIN NO JOOMLA
    private $plgFullFileFolder;                     // PATH COMPLETO PARA DIRETÓRIO PADRÃO DOS ARQUIVOS DO PLUGIN
    private $plgFullPath;                           // PATH COMPLETO PARA DIRETÓRIO PADRÃO DE INSTALAÇÃO DO PLUGIN
    private $plgFullUrl;                            // URL COMPLETA PARA DIRETÓRIO A SER LISTADO
    private $plgFileDirectory;                      // DIRETORIO RAIZ A SER LISTADO
    private $plgUserParams;                         // PARAMETROS PASSADOS PELOS ARTIGO
    private $sitePath;                              // PATH DO DIRETÓRIO ROOT DO SITE
    private $siteUrl;                               // URL DO SITE
    private $document;                              // VARIAVEL JFACTORY
    private $filesArr;                              // ARRAY COM O NOMES DOS ARQUIVOS EXISTENTES NO PLG_CFG
    private $dataArr;                               // ARRAY COM OS DADOS DOS ARQUIVOS EXISTENTES NO PLG_CFG
    private $dirLevel;                              // VARIAVEL CONTADORA DE RECURSIVIDADE DIRETÓRIO    
    private $currDir;                               // DIRETORIO ATUAL DA LISTAGEM
    private $currFileExt;                           // EXTENSÃO ATUAL DO ARQUIVO
    private $extension;                             // EXTENSÕES PERMITIDAS PARA EXIBIÇÃO
    private $responsavel;                           // SETOR RESPONSAVEL PELA PUBLICAÇÃO DOS ARQUIVOS.
    private $ordenacao;                             // RECEBE O TIPO DE ORDENAÇÃO.
    private $recursiveTimes = 2;                    // QUANTIDADE DE NIVEIS DE RECURSIVIDADE
    private $mtimeDir = 0;                          // RECEBE TEMPORARIA DATA DA ULTIMA MODIFICAÇÃO DO DIRETÓRIO
    private $lastMod;                               // RECEBE DATA DA ULTIMA MODIFICAÇÃO DO DIRETÓRIO
    private $teste;
    public $extText = array('doc', 'docx', 'odt', 'ods', 'xls', 'xlxs', 'pdf');
    public $extImage = array('png', 'bmp', 'jpg', 'gif', 'jpeg');
    public $extPreview = array('png', 'bmp', 'jpg', 'gif', 'jpeg', 'pdf');
    public $extDeny = array('ini', 'bat', 'exe', 'msi');
    public $extMimeTypes = array(
        '3g2', '3gp',
        'ai', 'air', 'asf', 'avi',
        'bib',
        'cls', 'csv',
        'deb', 'djvu', 'dmg', 'doc', 'docx', 'dwf', 'dwg',
        'eps', 'epub', 'exe',
        'f', 'f77', 'f90', 'flac', 'flv',
        'gif', 'gz',
        'ico', 'indd', 'iso',
        'jpg', 'jpeg',
        'log',
        'm4a', 'm4v', 'midi', 'mkv', 'mov', 'mp3', 'mp4', 'mpeg', 'mpg', 'msi',
        'odp', 'ods', 'odt', 'oga', 'ogg', 'ogv',
        'pdf', 'png', 'pps', 'ppsx', 'ppt', 'pptx', 'psd', 'pub', 'py',
        'qt',
        'ra', 'ram', 'rar', 'rm', 'rpm', 'rtf', 'rv',
        'skp', 'spx', 'sql', 'sty',
        'tar', 'tex', 'tgz', 'tiff', 'ttf', 'txt',
        'vob',
        'wav', 'wmv',
        'xls', 'xlsx', 'xml', 'xpi',
        'zip',
    );

    /**
     * Constructor
     *
     * @access      protected
     * @param       object  $subject The object to observe
     * @param       array   $config  An array that holds the plugin configuration
     */
    public function __construct(& $subject, $config) {
        parent::__construct($subject, $config);
        $this->plgFileFolder = $this->params->get('rootfolder') . '/';
        $this->sitePath = JPATH_SITE . '/';
        $this->siteUrl = JURI::root(true);
        $this->plgFullPath = $this->siteUrl . self::PLG_PATH;
        $this->plgFullFileFolder = $this->sitePath . $this->plgFileFolder;
        $this->plgFullUrl = $this->siteUrl . "/" . $this->plgFileFolder;
        $this->document = JFactory::getDocument();
        $this->dirLevel = 0;
        $this->currDir = "";
    }

    public function onContentPrepare($context, &$article, &$params, $limitstart = 0) {

        if (strpos($article->text, self::PLG_PARAM) === false) {
            return;
        }

        $regex = "#{" . self::PLG_PARAM . "}(.*?){/" . self::PLG_PARAM . "}#is";

        preg_match_all($regex, $article->text, $matches);

        if (!count($matches[0])) {
            return;
        }

        foreach ($matches[0] as $key => $match) {
            $tagcontent = preg_replace("/{.+?}/", "", $match);
            $this->plgUserParams = explode('|', $tagcontent);
            $this->plgFileDirectory = trim($this->plgUserParams[0]);
            $this->responsavel = array_key_exists(1, $this->plgUserParams) ? trim($this->plgUserParams[1]) : false;
            $this->extensions = array_key_exists(2, $this->plgUserParams) ? (trim($this->plgUserParams[2]) != 'a' ? trim($this->plgUserParams[2]) : false ) : false;
            $this->ordenacao = array_key_exists(3, $this->plgUserParams) ? trim($this->plgUserParams[3]) : false;
            $tagcontent = str_replace('|', "\|", $tagcontent);
        }

        $this->plgFullFileFolder .= $this->plgFileDirectory;

        $plg_html = $this->plgListAll($this->plgFullFileFolder);
        $plg_html .= $this->renderLastMod($this->mtimeDir);
        $plg_html .= "<div id='iframe_down' data-path='" . $this->siteUrl . "'></div>";
        $plg_html = utf8_encode($plg_html);
        $this->document->addStyleSheet($this->plgFullPath . self::FC_STYLE, 'text/css', 'screen');
        $this->document->addScript($this->plgFullPath . self::FC_JS);
        $this->document->addStyleSheet($this->plgFullPath . self::PLG_STYLE, 'text/css');
        $this->document->addScript($this->plgFullPath . self::PLG_JS);


        $article->text = preg_replace("#{" . self::PLG_PARAM . "}" . $tagcontent . "{/" . self::PLG_PARAM . "}#s", $plg_html, $article->text);

        return true;
    }

    public function plgListAll($directory, $extensions = array()) {

        if (substr($directory, -1) == "/")
            $directory = substr($directory, 0, strlen($directory) - 1);
        $code .= $this->plgListAllRecursive($directory, $extensions);
        return $code;
    }

    public function plgListAllRecursive($directory, $extensions = array(), $first_call = true) {
        $file = scandir($directory);


        natcasesort($file);
        // Make directories first
        $files = $dirs = array();
        foreach ($file as $this_file) {
            if ($this_file != "." && $this_file != "..") {
                if (is_dir("$directory/$this_file")) {
                    if ($this->dirLevel < 2)
                        $dirs[] = $this_file;
                } else {
                    $files[] = $this_file;
                }
            }
        }



        // $file = array_merge($dirs, $files);
        $dirRender = "";
        $filesRender = "";

        if (count($dirs) > 0) {
            foreach ($dirs as $dir) {
                if (($dir != "." && $dir != "..")) {
                    $dirRender .= $this->renderDir($directory, $dir);
                }
            }
        }
        unset($this->filesArr);
        unset($this->dataArr);
        if (file_exists($directory . self::PLG_CFG)) {
            $lines = file($directory . self::PLG_CFG);
            if (count($lines) > 0) {
                $this->filesArr = array();
                $this->dataArr = array();
                foreach ($lines as $line_num => $line) {
                    $temp_files = explode('|', $line);
                    if (count($temp_files) > 1 && file_exists($directory . "/" . trim($temp_files[0]))) {
                        $this->filesArr[$line_num] = trim($temp_files[0]);
                        $this->dataArr[$line_num] = array('nome_exibicao' => trim($temp_files[1]), 'descricao' => trim($temp_files[2]));
                    }
                }
            }
        }
        $files = $this->orderFiles($files, $directory);
        $files = $this->filterFiles($files);

        /*ob_start();
        var_dump($this->teste);
        $result = ob_get_clean();*/
        if (count($files) > 0) {

            foreach ($files as $nfile) {
                if (($nfile != "." && $nfile != "..")) {
                    $filesRender .= $this->renderFile($nfile);
                }
            }
        } else {
            $filesRender .= "<div class='row-fluid'><i>Pasta Vazia</i></div>";
        }
        $fileTree = "<div style='font-size:12px'>"
                //. $result
                . $dirRender
                . $filesRender
                . "</div>";
        $this->getMtimeDir($directory);
        return $fileTree;
    }

    public function renderDir($directory, $currDir) {
        $htmlDir = str_replace(" ", "_", $currDir);
        $showDir = utf8_decode(mb_strtoupper($currDir));
        $this->dirLevel++;
        $this->currDir .= "/" . $currDir;
        $contentDir = $this->plgListAllRecursive("$directory/$currDir", $extensions, false);
        $this->currDir = $this->removeLastDir($this->currDir);
        $this->dirLevel--;
        $renderDir = "<div class=\"row-fluid panel panel-default\">"
                . "<div class=\"panel-heading\"><a data-toggle=\"collapse\" href=\"#" . $htmlDir . "\">" . $showDir . "</a></div>"
                . "<div id=\"" . $htmlDir . "\" class=\"panel-collapse collapse\"><div class=\"panel-body\">"
                . $contentDir
                . "</div></div></div>";
        return $renderDir;
    }

    public function removeLastDir($currDir) {
        $currDir = explode('/', $currDir);
        $tempDir = array_pop($currDir);
        $currDir = implode("/", $currDir);
        return $currDir;
    }

    public function renderFile($file) {
        $key_file = (count($this->filesArr) > 0) ? array_search($file, $this->filesArr) : false;
        $this->currFileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $fileData = "<div class='row-fluid' style='border-bottom: 1px solid #ccc; margin: 5px 0 10px 0; min-height: 60px; '><div class='span8'><div><b>";
        $fileData .=($key_file !== false || strlen($this->dataArr[$key_file]['nome_exibicao'] > 0)) ? $this->dataArr[$key_file]['nome_exibicao'] : $file;
        $fileData .= "</b></div>"
                . $this->extensionIcon($file)
                . $this->previewFile($file)
                . $this->downloadFile($file);
        $fileData .=($key_file !== false || strlen($this->dataArr[$key_file]['descricao'] > 0)) ? "<div class='span12' style='height: 25px'><i>" . $this->dataArr[$key_file]['descricao'] . "</i></div>" : "";
        $fileData .= "</div>";
        return $fileData;
    }

    public function extensionIcon($file) {
        $imgPath = $this->siteUrl . self::PLG_IMG_PATH;
        if (in_array($this->currFileExt, $this->extMimeTypes)) {
            $iconFile = '<div class="row-fluid"><img src="' . $imgPath . $this->currFileExt . '-icon-16x16.png" style="padding: 0 5px" />' . $file . '</div></div>';
        } else {
            $iconFile = '<div class="row-fluid"><img src="' . $imgPath . 'txt-icon-16x16.png" style="padding: 0 5px" />' . $file . '</div></div>';
        }
        return $iconFile;
    }

    public function previewFile($file) {
        if (in_array($this->currFileExt, $this->extPreview)) {
            $class = ($this->currFileExt == 'pdf') ? '-pdf' : '';
            $previewButton = "<div class='span4 text-right' style=' vertical-align: top'>"
                    . "<button class='btn-small btn-warning bt-preview" . $class . "' data-file='" . $this->plgFullUrl . $this->plgFileDirectory . $this->currDir . "/" . $file . "' style='margin-right:5px'>Preview</button>";
        } else {
            $previewButton = "<div class='span4 text-right' style=' vertical-align: top'>";
        }
        return $previewButton;
    }

    public function downloadFile($file) {

        $btnDownload = "<button class='btn-small btn-success bt-download' data-file='" . base64_encode($this->plgFullFileFolder . $this->currDir . "/" . $file) . "' >Download</button></div>";
        return $btnDownload;
    }

    public function filterFiles($arrFiles = array()) {
        $allowExt = false;
        if ($this->extensions) {
            $allowExt = array();
            $plgExt = explode(",", $this->extensions);
            if ($inArr = array_search('text', $plgExt) !== false) {
                $plgExt = array_diff($plgExt, array('text'));
                $allowExt = array_merge($this->extText, $allowExt);
            }
            if ($inArr = array_search('image', $plgExt) !== false) {
                $plgExt = array_diff($plgExt, array('image'));
                $allowExt = array_merge($this->extImage, $allowExt);
            }
            $allowExt = array_merge($allowExt, $plgExt);
        }
        foreach ($arrFiles as $key => $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if ($allowExt && (!in_array(strtolower($ext), $allowExt)))
                unset($arrFiles[$key]);
            if (in_array(strtolower($ext), $this->extDeny))
                unset($arrFiles[$key]);
        }
        return $arrFiles;
    }

    public function orderFiles($arrFiles = array(), $directory) {
        switch ($this->ordenacao) {
            case 'm': {
                    $tempFiles = array_diff($arrFiles, $this->filesArr);
                    $orderFiles = array_merge($this->filesArr, $tempFiles);
                    break;
                }
            case 'c': {                    
                    foreach ($arrFiles as $file) {
                          $ctime = filectime($directory . "/" . $file);
                          $mtime = filemtime($directory . "/" . $file);
                          $ftime = $ctime > $mtime ? $ctime : $mtime;
                          
                          while (array_key_exists($ftime, $orderFiles)) {
                              $ftime--;
                          }
                          $orderFiles[$ftime] = $file;
                    }
                    
                    krsort($orderFiles);                    
                    break;
                }

            default: {
                    $orderFiles = $arrFiles;
                }
        }
        return $orderFiles;
    }

    public function getMtimeDir($directory) {
        $tempMtime = filemtime($directory);
        if ($this->mtimeDir < $tempMtime) {
            $this->mtimeDir = $tempMtime;
        }
    }

    public function renderLastMod($time) {
        $last = '<div class="row-fluid text-right" style="font-size:12px">';
        $last .= $this->responsavel ? '<i>PUBLICAÇÕES SOB RESPONSABILIDADE ' . $this->responsavel . '</i><br />' : "";
        $last .= '<b>Última alteração: ' . date('d-m-Y H:i:s', $time) . ' </b></div>';

        return $last;
    }

}

?>