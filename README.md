# Yii1 summernote widget 
[Yii](http://www.yiiframework.com) [Summernote](http://summernote.org) widget. Super simple WYSIWYG editor on Bootstrap

example of using
```php
  $this->widget('ext.summernote.SummernoteWidget', array(
                'model' => $model,
                'attribute' => 'description',
                'url' => Yii::app()->createUrl('upload'),
                'clientOptions' => array(
                    'lang' => 'ru-RU',
                    'height' => 500,
                ),
                'plugins' => array('video')
            ));
```

and in controller
```php
    public function actions()
    {
        return CMap::mergeArray(array(
            'insertPhoto' => array(
                'class' => 'ext.summernote.UploadAction',
                'path' => 'webroot.uploads.goods',
                'url' => '/uploads/goods/'
            )
                ), parent::actions());
    }
```


See [clientOptions](http://summernote.org/#/example) 

See [Yii2 Summernote widget](https://github.com/zelenin/yii2-summernote-widget)
