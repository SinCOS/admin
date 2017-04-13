<?php

namespace App\Validation;
use Respect\Validation\Exceptions\NestedValidationException;

class Validate {
    protected $errors;
    
    public function valildate($request,array $rules){
        foreach($rules as $field => $rule){
            try{
            
            }catch(Exception $e){
                $this->errors[$field] = $e->getMessages();
            }
        }
        
    }
    public function fail(){
        return !empty($this->errors);
    }
}