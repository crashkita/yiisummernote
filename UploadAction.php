<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UploadAction
 *
 * @author Trubachev Denis
 */
class UploadAction extends CAction
{
    public $path = 'webroot.uploads';
    public $url = '/uploads/';
    public function run()
    {
        $file = CUploadedFile::getInstanceByName('file');
        $result = array('status' => 'success');
        if (is_null($file)){
           
        } else {
            $fileName = md5($file->getName() . time()) . '.' . $file->getExtensionName();
            $fullPath = Yii::getPathOfAlias($this->path) . DIRECTORY_SEPARATOR . $fileName; 
            $file->saveAs($fullPath);
            $result['url'] = $this->url . $fileName;
        }
        echo CJSON::encode($result);
        Yii::app()->end();
    }
}
