<?php

/**
 * Description of SummernoteWidget
 *
 * @author Trubachev Denis
 */
class SummernoteWidget extends CInputWidget
{

    /** @var array */
    private $defaultOptions = array('class' => 'form-control');

    /** @var array */
    private $defaultClientOptions = array(
        'height' => 200,
        'codemirror' => array(
            'theme' => 'monokai'
        ),
        'toolbar' => array(
            array('style', array('bold', 'italic', 'underline', 'clear')),
            array('fontname', array('fontname')),
            array('fontsize', array('fontsize')),
            array('color', array('color')),
            array('para', array('ul', 'ol', 'paragraph')),
            array('height', array('height')),
            array('table', array('table')),
            array('insert', array('link', 'picture', 'hr')),
            array('view', array('fullscreen', 'codeview')),
            array('help', array('help'))
        )
    );

    /** @var array */
    public $options = array();

    /** @var array */
    public $clientOptions = array();

    /** @var array */
    public $plugins = array();

    /**
     * Url for upload file
     * @var string 
     */
    public $url = '';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->options = array_merge($this->defaultOptions, $this->options);
        $this->clientOptions = array_merge($this->defaultClientOptions, $this->clientOptions);
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerAssets();
        $this->options['id'] = $this->getId();
        if ($this->hasModel()) {
            echo CHtml::activeTextArea($this->model, $this->attribute, $this->options);
        } else {
            echo CHtml::textArea($this->name, $this->value, $this->options);
        }
        $this->registrationJS();
    }

    /**
     * Registrate all assets for widget 
     */
    private function registerAssets()
    {
        if (array_key_exists('codemirror', $this->clientOptions)) {
            $this->registerCodemirror();
        }

        $this->registerFontAwesome();
        $this->registerAssetsBase();
        $this->registerLang();
        $this->registerPlugins();
    }

    /**
     * Registration base assets for widget
     */
    private function registerAssetsBase()
    {
        $postfix = YII_DEBUG ? '' : '.min';
        $clientScript = Yii::app()->clientScript;
        $clientScript->registerPackage('bootstrap');
        $assetsDir = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;

        $clientScript->registerCssFile(Yii::app()->assetManager->publish($assetsDir
                        . 'css/summernote.css'));
        $js = 'summernote' . $postfix . '.js';
        $clientScript->registerScriptFile(Yii::app()->assetManager->publish(
                        $assetsDir . 'js/' . $js));
    }

    /**
     * Registration codemirror thema
     */
    private function registerCodemirror()
    {
        $clientScript = Yii::app()->clientScript;
        $assetsDir = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
        $clientScript->registerCssFile(Yii::app()->assetManager->publish($assetsDir
                        . 'codemirror/lib/codemirror.css'));
        $clientScript->registerCssFile(Yii::app()->assetManager->publish($assetsDir
                        . 'codemirror/theme/monokai.css'));

        $clientScript->registerScriptFile(Yii::app()->assetManager->publish($assetsDir
                        . '/codemirror/lib/codemirror.js'));
        $clientScript->registerScriptFile(Yii::app()->assetManager->publish($assetsDir
                        . 'codemirror/mode/xml/xml.js'));
        //$clientScript->registerScript($assetsDir, $script);
    }

    /**
     * Registration assets for font
     */
    private function registerFontAwesome()
    {
        $postfix = YII_DEBUG ? '' : '.min';
        $clientScript = Yii::app()->clientScript;
        $assetsDir = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
        $css = '/css/font-awesome' . $postfix . '.css';
        $url = Yii::app()->assetManager->publish($assetsDir
                . 'font-awesome');
        $clientScript->registerCssFile($url . $css);
    }

    /**
     * Registration language file
     */
    private function registerLang()
    {
        if (array_key_exists('lang', $this->clientOptions)) {
            $lang = $this->clientOptions['lang'];
            $this->options['lang'] = $lang;
            $clientScript = Yii::app()->clientScript;
            $assetsDir = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
            $clientScript->registerScriptFile(Yii::app()->assetManager->publish($assetsDir
                            . 'js/lang/summernote-' . $lang . '.js'));
        }
    }

    /**
     * JS handler for upload file
     * @return type
     */
    private function registerUpload()
    {
        $id = $this->getId();
        $js = <<<JS
js:function(files, editor, welEditable) {
    var file = files[0];        
    data = new FormData();
    data.append("file", file);
    $.ajax({
        data: data,
        type: "POST",
        url: "$this->url",
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            var data = JSON.parse(data);
            $('#$id').summernote("insertImage", data.url, data.filename);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus+" "+errorThrown);
        }
    });
}
JS;
        return $js;
    }

    private function registrationJS()
    {
        $id = $this->getId();
        if (!empty($this->url)) {
            $this->clientOptions['onImageUpload'] = $this->registerUpload();
        }
        $clientOptions = empty($this->clientOptions) ? null : CJavaScript::encode($this->clientOptions);
        Yii::app()->clientScript->registerScript(
                $id, 'jQuery( "#' . $id . '" ).summernote(' . $clientOptions . ');',
                CClientScript::POS_LOAD
        );
    }

    public function getId($autoGenerate = true)
    {
        if (isset($this->options['id'])) {
            return $this->options['id'];
        } else {
            return parent::getId($autoGenerate);
        }
    }

    /**
     * Registration plugins
     */
    public function registerPlugins()
    {
        $pluginsDir = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'plugin' . DIRECTORY_SEPARATOR;
        $clientScript = Yii::app()->clientScript;
        if (!empty($this->plugins) && is_array($this->plugins)) {
            foreach ($this->plugins as $plugin) {
                $clientScript->registerScriptFile(
                        Yii::app()->assetManager->publish($pluginsDir
                                . 'summernote-ext-' . $plugin . '.js'));
                if ($plugin == 'video') {
                    $this->clientOptions['toolbar'][] = array('insert2', array('video'));
                }
            }
        }
    }

}
