# Yii1 summernote widget 
[Yii](http://www.yiiframework.com) [Summernote](http://summernote.org) widget. Super simple WYSIWYG editor on Bootstrap

example of using
```php
<$this->widget('ext.summernote.SummernoteWidget', array(
                    'model' => $model,
                    'attribute' => 'description',
                    'url' => Yii::app()->createUrl('/company/image/insertPhoto'),
                    'clientOptions' => array(
                        'lang' => 'ru-RU'
                    )
                ));
```
See [clientOptions](http://summernote.org/#/example)
See [Yii2 Summernote widget](https://github.com/zelenin/yii2-summernote-widget)
