<?php
namespace shogchat\page;

class index extends Page{
    public function showPage(){
        echo $this->getTemplateEngine()->render($this->getTemplateSnip("page"), [
            "title" => "Welcome!",
            "content" => $this->getTemplateEngine()->render($this->getTemplate(), [])
        ]);
        //echo $this->getTemplateEngine()->render($this->getTemplate(), []);
    }
    public function hasPermission(){
        return true;
    }

}