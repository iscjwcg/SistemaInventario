<?php

header('Content-Type: text/html; charset=utf-8');

class BaseControl
{
    public function BaseControl()
    {
        echo utf8_encode($this->htmlStrucure);
    }

    protected $id = "";
    public function ID($value)
    {
        if(isset($value))
        {
            $this->id = $value;
        }
        else
        {
            return $this->id;
        }
    }

    protected $htmlStrucure = "";
    public function HTML($value)
    {
        if(isset($value))
        {
            $this->htmlStrucure = $value;
        }
        else
        {
            return $this->htmlStrucure;
        }
    }

    protected  $isVisible = true;
    public function IsVisible($value)
    {
        if(isset($value))
        {
            $this->isVisible = $value;
        }
        else
        {
            return $this->isVisible;
        }
    }

    protected $isEnable = true;
    public function IsEnable($value)
    {
        if(isset($value))
        {
            $this->isEnable = $value;
        }
        else
        {
            return $this->isEnable;
        }
    }

    protected  $isEditable = true;
    public function IsEditable($value)
    {
        if(isset($value))
        {
            $this->isEditable = $value;
        }
        else
        {
            return $this->isEditable;
        }
    }
}

class Toolbar extends BaseControl
{
    protected $id = "";
    protected $caption = "";
    protected $htmlTemplate = '<div class="control-group">'
                                .'<label class="control-label" for="inputEmail">%2$s</label>'
                                .'<div class="controls">'
                                .'<input type="text" id="%1$s" name="%1$s" placeholder="Email" %3$d disabled="disabled">'
                                .'</div>'
                                .'</div>';

    public function Toolbar($caption)
    {
        if(isset($caption))
        {
            $this->Caption($caption);
        }

        if($this->caption != "")
        {
            parent::HTML(sprintf($this->htmlTemplate, $this->caption));
            parent::BaseControl();
        }
    }

    public function Caption($value)
    {
        if(isset($value))
        {
            $this->caption = $value;
        }
        else
        {
            return $this->caption;
        }
    }
}

class Field extends BaseControl
{
    protected $id = "";
    protected $label = "";
    protected $tipo = "varchar";
    protected $size = 0;
    protected $search = "";
    protected $editable = true;
    protected $required = false;

    protected $htmlTemplate = '<div class="w2ui-field w2ui-span8 %6$s">'
                                .'<label>%2$s</label>'
                                .'<div><%input%>'
                                .'<%search%></div></div>';
    protected $searchTemplate = '<span id="search_%1$s" style="display: none;" class="imgsearch" role="search" search="%4$s"><a href="#modalsearch" rel="modal:open"><img src="./styles/images/search.png" alt="Search" height="20" width="20"></a></span>';
    
    protected $inputTemplate = array(
        "varchar" => '<input id="%1$s" name="%1$s" type="text" maxlength="%3$d" style="width: 200px !important; padding-right:25px;" class="" role="field" disabled %5$s></inpu>',
        "date" => '<input id="%1$s" name="%1$s" type="eu-date" maxlength="%3$d" style="width: 200px !important;" class="" role="field" disabled %5$s>',
        "bit" => '<input id="%1$s" name="%1$s" type="checkbox" style="width: 50px !important; margin-top: 6px;" class="" role="field" size="30" disabled %5$s></inpu>',
        "int" => '<input id="%1$s" name="%1$s" type="number" maxlength="%3$d" style="width: 100px !important;" class="" role="field" disabled %5$s step="1"></inpu>',
        "decimal" => '<input id="%1$s" name="%1$s" type="number" maxlength="%3$d" style="width: 100px !important;" class="" role="field" disabled %5$s step="0.0001"></inpu>');

    public function Field($id, $label, $tipo, $size, $search, $editable, $required)
    {
        if(isset($id))
        {
            $this->id = $id;
            parent::ID($id);
        }

        if(isset($label))
        {
            $this->Label($label);
        }
        
        if(isset($tipo))
        {
            $this->Tipo($tipo);
        }

        if(isset($size))
        {
            $this->Size($size);
        }
        
        if(isset($search))
        {
            $this->Seach($search);
        }

        if(isset($editable))
        {
            $this->Editable($editable);
        }
        
        if(isset($required))
        {
            $this->Required($required);
        }

        if($this->id != "" && $this->label != "" && $this->size > 0)
        {
            $this->htmlTemplate = str_replace("<%input%>", $this->inputTemplate[$this->tipo], $this->htmlTemplate);
            $this->htmlTemplate = str_replace("<%search%>", (strtolower($this->search) != "none" && $this->tipo == "varchar" ? $this->searchTemplate : ""), $this->htmlTemplate);
            parent::HTML(sprintf($this->htmlTemplate, $this->id, $this->label, $this->size, $this->search, ($this->editable ? "" : "readonly"), ($this->required ? "required" : "")));
            parent::BaseControl();
        }
    }

    public function Label($value)
    {
        if(isset($value))
        {
            $this->label = $value;
        }
        else
        {
            return $this->label;
        }
    }
    
    public function Tipo($value)
    {
        if(isset($value))
        {
            $this->tipo = $value;
        }
        else
        {
            return $this->tipo;
        }
    }

    public function Size($value)
    {
        if(isset($value))
        {
            $this->size = $value;
        }
        else
        {
            return $this->size;
        }
    }
    
    public function Seach($value)
    {
        if(isset($value))
        {
            $this->search = $value;
        }
        else
        {
            return $this->search;
        }
    }

    public function Editable($value)
    {
        if(isset($value))
        {
            $this->editable = $value;
        }
        else
        {
            return $this->editable;
        }
    }
    
    public function Required($value)
    {
        if(isset($value))
        {
            $this->required = $value;
        }
        else
        {
            return $this->required;
        }
    }
}
?>