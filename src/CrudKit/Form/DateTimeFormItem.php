<?php

namespace CrudKit\Form;

class DateTimeFormItem extends HorizontalItem{

    public function renderInternal()
    {
        $ngModel = $this->form->getNgModel();
        $value = isset($this->config['value']) ? $this->config['value'] : "";

        return <<<COMP
        <div class="input-group">
        <input type="date" class="form-control" ng-model="$ngModel.{$this->key}" />
        </div>
COMP;
    }
}